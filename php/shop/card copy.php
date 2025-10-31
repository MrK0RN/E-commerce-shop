<?php
$id = $_GET["id"];
$card_id = $_GET["card"];
include "../system/db.php";
$cards = pgQuery('SELECT * FROM cards where good_id = '.$id.';');
if (empty($card_id)){
    $card_id = $cards[0]["id"];
}

// Получаем размеры из таблицы sizes
$sizes = pgQuery('SELECT * FROM sizes where good_id = '.$id.' ORDER BY id;');

// Вспомогательная функция получения списка изображений для карточки
function getCardImages($cardId) {
    $cardImagesPath = "../data/cards/" . $cardId;
    $images = [];
    if (is_dir($cardImagesPath)) {
        $files = scandir($cardImagesPath);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                $images[] = $file;
            }
        }
        sort($images);
    }
    if (empty($images)) {
        $fallbackPath = "../images/2";
        if (is_dir($fallbackPath)) {
            $files = scandir($fallbackPath);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..' && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                    $images[] = $file;
                }
            }
            sort($images);
            $cardImagesPath = $fallbackPath;
        }
    }
    // Возвращаем абсолютные пути относительно текущей страницы
    $fullPaths = [];
    foreach ($images as $img) {
        $fullPaths[] = $cardImagesPath . '/' . $img;
    }
    return $fullPaths;
}

// Легкий API для получения картинок по цвету/карточке
if (isset($_GET['action']) && $_GET['action'] === 'images' && isset($_GET['card'])) {
    header('Content-Type: application/json; charset=utf-8');
    $cid = intval($_GET['card']);
    echo json_encode([
        'images' => getCardImages($cid)
    ]);
    exit;
}

$good = pgQuery('SELECT * FROM goods where id = '.$id.';')[0];
$card = pgQuery('SELECT * FROM cards where id = '.$card_id.';')[0];
$params = pgQuery("SELECT * FROM params WHERE good_id = ".$good["id"].";");
$options = pgQuery("SELECT * FROM options WHERE good_id = ".$good["id"].";");

function addOrderToDatabase($orderData) {
    try {
        $query = "
            INSERT INTO orders (
                product_name, color, size, base_price, size_addition, final_total,
                first_name, last_name, phone, email, address, order_reference, created_at
            ) VALUES ('".$orderData['product']."', '".$orderData['color']."', '".$orderData['size']."', ".$orderData['base_price'].", ".$orderData['size_addition'].", ".$orderData['final_total'].", '".$orderData['first_name']."', '".$orderData['last_name']."', '".$orderData['phone']."', '".$orderData['email']."', '".$orderData['address']."', '".$orderData['order_reference']."', NOW())
        ";
        
        $result = pgQuery($query);
        return $result !== false;
        
    } catch (Exception $e) {
        error_log("Error adding order to database: " . $e->getMessage());
        return false;
    }
}

// Обработка AJAX запроса на создание заказа
if (isset($_POST['action']) && $_POST['action'] === 'create_order') {
    header('Content-Type: application/json; charset=utf-8');
    
    $orderData = [
        'product' => $_POST['product'],
        'color' => $_POST['color'],
        'size' => $_POST['size'],
        'base_price' => floatval($_POST['base_price']),
        'size_addition' => floatval($_POST['size_addition']),
        'final_total' => floatval($_POST['final_total']),
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'phone' => $_POST['phone'],
        'email' => $_POST['email'],
        'address' => $_POST['address'],
        'order_reference' => 'RS' . substr(time(), -6) . rand(100, 999)
    ];
    
    $response = ['success' => false, 'message' => '', 'order_ref' => $orderData['order_reference']];
    
    // Добавляем заказ в БД
    $dbSuccess = addOrderToDatabase($orderData);
    
    if ($dbSuccess) {
        $response['success'] = true;
        $response['message'] = 'Order successfully created';
        
        // Отправляем на почту (существующая логика)
        try {
            $mailResponse = file_get_contents('http://localhost:8900/mail/api', false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/json',
                    'content' => json_encode($orderData)
                ]
            ]));
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
        }
    } else {
        $response['message'] = 'Error saving order to database';
    }
    
    echo json_encode($response);
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$good["name"]?> - Купить</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/cards.css?v=1234">
</head>
<body style="background: white">
    <?php
    include "../assets/header_black.php";?>
    <div class="container">
        <div class="product-card">
            <div class="left-column">
                <div class="gallery">
                    <div class="thumbnails">
                        <?php
                            $currentImages = getCardImages($card['id']);
                            $firstImage = true;
                            foreach ($currentImages as $index => $imagePath) {
                                $activeClass = $firstImage ? ' active' : '';
                                echo '<img src="' . $imagePath . '" alt="Thumbnail ' . ($index + 1) . '" class="thumbnail' . $activeClass . '" data-image="' . $imagePath . '">';
                                $firstImage = false;
                            }
                        ?>
                    </div>
                    <div class="main-image">
                        <span class="image-badge">Bestseller</span>
                        <?php
                        // Устанавливаем главное изображение (первое из списка)
                        if (!empty($currentImages)) {
                            $mainImagePath = $currentImages[0];
                            echo '<img src="' . $mainImagePath . '" alt="Main Product Image" id="main-image">';
                        } else {
                            // Fallback изображение, если нет картинок
                            echo '<img src="https://via.placeholder.com/600x500?text=No+Image" alt="Main Product Image" id="main-image">';
                        }
                        ?>
                        <div class="image-nav">
                            <button class="tooltip" data-tooltip="Previous"><i class="fas fa-chevron-left"></i></button>
                            <button class="tooltip" data-tooltip="Next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
                
                <!-- Перенос гарантии под фото -->
                <div class="warranty-section">
                    <div class="warranty"><i class="fas fa-shield-alt"></i> Warranty: 5 years</div>
                    <div class="rating"><i class="fas fa-star"></i> <?=$good["score"]?> (247 reviews)</div>
                </div>
                
                <div class="description">
                    <h3>Product Description</h3>
                    <p><?=$good["description"]?></p>
                    
                    <div class="features">
                        <div class="feature"><i class="fas fa-check-circle"></i> Free shipping</div>
                        <div class="feature"><i class="fas fa-check-circle"></i> 5-year warranty</div>
                        <div class="feature"><i class="fas fa-check-circle"></i> 30-day returns</div>
                    </div>
                </div>
            </div>
            
            <div class="right-column">
                <h1><?=$good["name"]?></h1>
                
                <div class="params-and-options">
                    <div class="params-group">
                        <div class="params-header">Specifications:</div>
                        <ul class="params">
                            <?php 
                            foreach ($params as $param){
                                echo '<li>'.$param["text"].'</li>';
                            }
                            ?>
                        </ul>
                    </div>
                    
                    <div class="options-group">
                        <div class="options__title">Configuration Options:</div>
                        <?php
                        foreach ($options as $option){
                            echo '<div class="option checked"><div>'.$option["text"].'</div></div>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- Блок выбора размера -->
                <div class="size-options">
                    <h3>Select Size <span class="required-asterisk">*</span></h3>
                    <div class="size-options-container">
                        <?php 
                        foreach ($sizes as $size){
                            $classes = 'size-option';
                            echo '<div class="'.$classes.'" data-size-id="'.$size["id"].'" data-price-addition="'.$size["price_addition"].'">
                                    <div class="size-name">'.$size["size_name"].'</div>
                                    <div class="size-price">+$'.formatPrice($size["price_addition"]).'</div>
                                  </div>';
                        }
                        ?>
                    </div>
                    <div class="size-error-message" style="color: #ef4444; font-size: 0.9rem; margin-top: 5px; display: none;">
                        Please select a size to continue
                    </div>
                </div>
                
                <div class="color-options">
                    <h3>Select Finish</h3>
                    <div class="color-carousel">
                        <?php 
                        foreach ($cards as $cad){
                            $isActive = ($cad["id"] == $card_id);
                            $classes = $isActive ? 'color-option active' : 'color-option';
                            echo '<div class="'.$classes.'" style="background-color: '.$cad["color"].';" data-color="'.$cad["color"].'" data-card-id="'.$cad["id"].'" data-price="'.$cad["price"].'"></div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="price">
                    <div class="old-price">$<?=formatPrice($good["old_price"])?></div>
                    <div class="new-price" id="current-price">$<?=formatPrice($card["price"])?> <span class="discount">-18%</span></div>
                    <div class="price-note" id="size-price-note">Select size to see financing options</div>
                </div>
                
                <div class="action-buttons">
                    <button class="order-btn" id="mainOrderBtn" disabled><i class="fas fa-rocket"></i> Select size to continue</button>
                    <button class="wishlist-btn tooltip" data-tooltip="Add to Wishlist"><i class="fas fa-heart"></i></button>
                </div>
            </div>
        </div>
        
        <!-- Заменяем карусели продуктов на FAQ -->
        <div class="faq-section">
            <div class="section-title">Frequently Asked Questions</div>
            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What is the delivery time for this product?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Standard delivery time is 2-3 weeks. Express delivery options are available for an additional fee.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Do you provide installation services?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we offer professional installation services. Our certified technicians can install your product within 1-2 business days after delivery.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What is covered under the 5-year warranty?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>The warranty covers manufacturing defects, mechanical failures, and structural issues. It does not cover damage from improper installation, accidents, or normal wear and tear.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>Can I customize the product dimensions?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes, we offer custom sizing options. Please contact our sales team for custom dimension requests and pricing.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept all major credit cards, PayPal, bank transfers, and offer financing options through our partners.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Галерея
        const thumbnailsContainer = document.querySelector('.thumbnails');
        let thumbnails = document.querySelectorAll('.thumbnail');
        const mainImage = document.getElementById('main-image');
        function bindThumbnailClicks(){
            thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    mainImage.src = this.getAttribute('data-image');
                    mainImage.style.opacity = 0;
                    setTimeout(() => { mainImage.style.opacity = 1; }, 150);
                });
            });
        }
        bindThumbnailClicks();
        
        // Обработка выбора цвета
        const colorOptions = document.querySelectorAll('.color-option');
        const apiImages = 'api/card.php?action=images';
        const currentPrice = document.getElementById('current-price');
        colorOptions.forEach(option => {
            option.addEventListener('click', async function() {
                colorOptions.forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                const selectedCardId = this.getAttribute('data-card-id');
                const price = this.getAttribute('data-price');
                updatePrice(price);

                try{
                    const res = await fetch(`${apiImages}&card=${encodeURIComponent(selectedCardId)}`, { credentials: 'same-origin' });
                    const data = await res.json();
                    const images = Array.isArray(data.images) ? data.images : [];
                    if (images.length){
                        // обновляем главное изображение
                        mainImage.src = images[0];
                        // пересобираем превью
                        thumbnailsContainer.innerHTML = images.map((src, idx) => `\n<img src="${src}" alt="Thumbnail ${idx+1}" class="thumbnail${idx===0?' active':''}" data-image="${src}">`).join('');
                        bindThumbnailClicks();
                    }
                }catch(e){
                    console.error(e);
                }
            });
        });

        // Обработка выбора размера
        const sizeOptions = document.querySelectorAll('.size-option');
        const mainOrderBtn = document.getElementById('mainOrderBtn');
        const sizeErrorMessage = document.querySelector('.size-error-message');
        
        sizeOptions.forEach(option => {
            option.addEventListener('click', function() {
                sizeOptions.forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                
                // Скрываем сообщение об ошибке
                sizeErrorMessage.style.display = 'none';
                
                // Активируем кнопку
                mainOrderBtn.disabled = false;
                mainOrderBtn.innerHTML = '<i class="fas fa-rocket"></i> Ready to go ahead';
                mainOrderBtn.style.background = '';
                mainOrderBtn.style.cursor = 'pointer';
                
                updatePrice();
            });
        });

        // Функция обновления цены с учетом размера и цвета
        function updatePrice(basePrice = null) {
            const activeColorOption = document.querySelector('.color-option.active');
            const activeSizeOption = document.querySelector('.size-option.active');
            
            if (!basePrice && activeColorOption) {
                basePrice = activeColorOption.getAttribute('data-price');
            }
            
            if (basePrice && currentPrice) {
                let finalPrice = parseFloat(basePrice);
                
                // Добавляем стоимость размера, если выбран
                if (activeSizeOption) {
                    const priceAddition = parseFloat(activeSizeOption.getAttribute('data-price-addition')) || 0;
                    finalPrice += priceAddition;
                }
                
                // Форматируем цену без лишних нулей
                const formattedPrice = formatPrice(finalPrice);
                const discountHTML = currentPrice.querySelector('.discount')?.outerHTML || '';
                currentPrice.innerHTML = `$${formattedPrice} ${discountHTML}`.trim();
                
                // Обновляем заметку о финансировании
                if (activeSizeOption) {
                    const monthlyPayment = Math.round(finalPrice / 10);
                    document.getElementById('size-price-note').textContent = `Financing available from $${monthlyPayment}/month`;
                } else {
                    document.getElementById('size-price-note').textContent = 'Select size to see financing options';
                }
            }
        }

        // Функция форматирования цены
        function formatPrice(price) {
            const num = parseFloat(price);
            return num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
        }
        
        // Обработка кнопки заказа
        const orderBtn = document.querySelector('.order-btn');
        
        orderBtn.addEventListener('click', function() {
            if (this.disabled) {
                // Показываем сообщение об ошибке, если размер не выбран
                sizeErrorMessage.style.display = 'block';
                return;
            }
            
            this.innerHTML = '<i class="fas fa-rocket"></i> Ready to go ahead';
            this.style.background = 'var(--success)';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-rocket"></i> Ready to go ahead';
                this.style.background = '';
            }, 2000);
        });
        
        // Обработка кнопки избранного
        const wishlistBtn = document.querySelector('.wishlist-btn');
        
        wishlistBtn.addEventListener('click', function() {
            this.innerHTML = this.innerHTML.includes('fa-heart') 
                ? '<i class="fas fa-check"></i>' 
                : '<i class="fas fa-heart"></i>';
                
            this.style.color = this.innerHTML.includes('fa-check') 
                ? 'var(--success)' 
                : '';
        });
        
        // Навигация по изображениям
        const prevBtn = document.querySelector('.image-nav button:first-child');
        const nextBtn = document.querySelector('.image-nav button:last-child');
        
        prevBtn.addEventListener('click', () => navigateImages(-1));
        nextBtn.addEventListener('click', () => navigateImages(1));
        
        function navigateImages(direction) {
            const currentIndex = Array.from(thumbnails).findIndex(thumb => thumb.classList.contains('active'));
            let newIndex = currentIndex + direction;
            
            if (newIndex < 0) newIndex = thumbnails.length - 1;
            if (newIndex >= thumbnails.length) newIndex = 0;
            
            thumbnails[newIndex].click();
        }

        // FAQ функциональность
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const item = question.parentElement;
                const answer = question.nextElementSibling;
                
                // Закрыть другие открытые FAQ
                document.querySelectorAll('.faq-item').forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector('.faq-answer').style.maxHeight = null;
                        otherItem.querySelector('.faq-question i').classList.remove('fa-chevron-up');
                        otherItem.querySelector('.faq-question i').classList.add('fa-chevron-down');
                    }
                });
                
                // Переключить текущий FAQ
                item.classList.toggle('active');
                if (item.classList.contains('active')) {
                    answer.style.maxHeight = answer.scrollHeight + "px";
                    question.querySelector('i').classList.remove('fa-chevron-down');
                    question.querySelector('i').classList.add('fa-chevron-up');
                } else {
                    answer.style.maxHeight = null;
                    question.querySelector('i').classList.remove('fa-chevron-up');
                    question.querySelector('i').classList.add('fa-chevron-down');
                }
            });
        });
    </script>

    <!-- Стили для FAQ -->
    <style>
    .warranty-section {
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .size-options {
        margin: 20px 0;
    }
    
    .size-options h3 {
        margin-bottom: 15px;
        font-size: 1.2rem;
        color: #333;
    }
    
    .required-asterisk {
        color: #ef4444;
    }
    
    .size-options-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 5px;
    }
    
    .size-option {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        background: white;
    }
    
    .size-option:hover {
        border-color: #86efac;
        background-color: #f0fdf4;
    }
    
    .size-option.active {
        border-color: #4ade80;
        background-color: #f0fdf4;
        box-shadow: 0 2px 8px rgba(74, 222, 128, 0.2);
    }
    
    .size-name {
        font-weight: 600;
        margin-bottom: 5px;
        color: #1f2937;
    }
    
    .size-price {
        color: #4ade80;
        font-weight: 500;
    }
    
    .faq-section {
        margin: 50px 0;
    }
    
    .faq-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .faq-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 10px;
        overflow: hidden;
    }
    
    .faq-question {
        padding: 20px;
        background: white;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #1f2937;
        transition: background-color 0.3s ease;
    }
    
    .faq-question:hover {
        background: #f8f9fa;
    }
    
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background: #f8f9fa;
    }
    
    .faq-answer p {
        padding: 20px;
        margin: 0;
        color: #6b7280;
        line-height: 1.6;
    }
    
    .faq-item.active .faq-question {
        background: #f0fdf4;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .order-btn:disabled {
        background-color: #9ca3af !important;
        cursor: not-allowed !important;
        opacity: 0.6;
    }
    </style>

    <!-- Pop-up форма (упрощенная версия с одним вопросом) -->
    <style>
    .order-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }

    .order-popup.active {
        display: flex;
    }

    .order-popup-content {
        background: white;
        border-radius: 16px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
    }

    .order-popup-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #666;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        z-index: 10;
    }

    .order-popup-close:hover {
        background: #f5f5f5;
        color: #333;
    }

    .order-popup-body {
        padding: 30px;
    }

    .order-question-section h3 {
        font-size: 1.5rem;
        color: #1f2937;
        margin-bottom: 20px;
        font-weight: 500;
        line-height: 1.4;
        text-align: center;
    }

    .contact-fields {
        display: grid;
        gap: 20px;
        margin-bottom: 30px;
    }

    .contact-field {
        display: flex;
        flex-direction: column;
    }

    .contact-field label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }

    .contact-field input {
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    .contact-field input:focus {
        outline: none;
        border-color: #4ade80;
    }

    .contact-field input:invalid {
        border-color: #ef4444;
    }

    .order-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #4ade80;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 2px solid #e5e7eb;
    }

    .order-btn-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #4ade80, #22c55e);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .order-btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
    }

    .order-btn-submit:disabled {
        background: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @media (max-width: 768px) {
        .order-popup-content {
            margin: 20px;
            width: calc(100% - 40px);
        }
        
        .order-popup-body {
            padding: 20px;
        }
    }
    </style>

    <!-- Pop-up форма -->
    <div class="order-popup" id="orderPopup">
        <div class="order-popup-content">
            <button class="order-popup-close" id="orderClose">&times;</button>
            
            <div class="order-popup-body">
                <div class="order-question-section" id="question1">
                    <h3>Contact Information</h3>
                    
                    <div class="contact-fields">
                        <div class="contact-field">
                            <label for="order-first-name">First Name *</label>
                            <input type="text" id="order-first-name" placeholder="John" required>
                        </div>
                        
                        <div class="contact-field">
                            <label for="order-last-name">Last Name *</label>
                            <input type="text" id="order-last-name" placeholder="Smith" required>
                        </div>
                        
                        <div class="contact-field">
                            <label for="order-phone">Phone Number *</label>
                            <input type="tel" id="order-phone" placeholder="+1 (555) 123-4567" required>
                        </div>
                        
                        <div class="contact-field">
                            <label for="order-email">Email Address *</label>
                            <input type="email" id="order-email" placeholder="your.email@example.com" required>
                        </div>
                        
                        <div class="contact-field">
                            <label for="order-address">Installation Address *</label>
                            <input type="text" id="order-address" placeholder="Your complete installation address" required>
                        </div>
                    </div>
                    
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Base price:</span>
                            <span id="final-base-price">$<?=formatPrice($card["price"])?></span>
                        </div>
                        <?php if (!empty($sizes)): ?>
                        <div class="summary-row">
                            <span>Size option:</span>
                            <span id="final-size-addition">$0</span>
                        </div>
                        <?php endif; ?>
                        <div class="summary-total">
                            <span>Final total:</span>
                            <span id="final-total">$<?=formatPrice($card["price"])?></span>
                        </div>
                    </div>
                    
                    <button class="order-btn-submit" id="orderSubmit">Complete Order</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Класс для управления pop-up заказа
    class OrderPopup {
        constructor() {
            this.popup = document.getElementById('orderPopup');
            this.openBtn = document.getElementById('mainOrderBtn');
            this.closeBtn = document.getElementById('orderClose');
            this.submitBtn = document.getElementById('orderSubmit');
            
            this.basePrice = this.getCurrentCardPrice();
            this.sizeAddition = 0;
            
            this.init();
        }
        
        getCurrentCardPrice() {
            const activeColorOption = document.querySelector('.color-option.active');
            if (activeColorOption) {
                return parseFloat(activeColorOption.getAttribute('data-price')) || <?=$card["price"]?>;
            }
            return <?=$card["price"]?>;
        }

        getCurrentSizeAddition() {
            const activeSizeOption = document.querySelector('.size-option.active');
            if (activeSizeOption) {
                return parseFloat(activeSizeOption.getAttribute('data-price-addition')) || 0;
            }
            return 0;
        }

        getCurrentCardName() {
            return '<?=$good["name"]?>';
        }

        getCurrentCardColor() {
            const activeColorOption = document.querySelector('.color-option.active');
            return activeColorOption ? activeColorOption.getAttribute('data-color') : '<?=$card["color"]?>';
        }

        getCurrentSizeName() {
            const activeSizeOption = document.querySelector('.size-option.active');
            return activeSizeOption ? activeSizeOption.querySelector('.size-name').textContent : 'Standard';
        }

        init() {
            // Открытие pop-up при клике на кнопку "Ready to go ahead"
            if (this.openBtn) {
                this.openBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    // Проверяем, выбран ли размер
                    const activeSizeOption = document.querySelector('.size-option.active');
                    if (!activeSizeOption) {
                        document.querySelector('.size-error-message').style.display = 'block';
                        return;
                    }
                    this.open();
                });
            }

            // Закрытие pop-up
            this.closeBtn.addEventListener('click', () => {
                this.close();
            });

            // Закрытие при клике вне формы
            this.popup.addEventListener('click', (e) => {
                if (e.target === this.popup) {
                    this.close();
                }
            });

            // Отправка формы
            this.submitBtn.addEventListener('click', () => {
                this.submitOrder();
            });

            // Валидация формы в реальном времени
            this.setupFormValidation();

            // Закрытие по ESC
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.popup.classList.contains('active')) {
                    this.close();
                }
            });

            // Слушаем изменения цветовых вариантов и размеров
            this.setupPriceChangeListeners();
        }

        setupFormValidation() {
            const inputs = document.querySelectorAll('#question1 input[required]');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    this.validateForm();
                });
            });
        }

        setupPriceChangeListeners() {
            // Слушаем изменения цветовых вариантов
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.addEventListener('click', () => {
                    this.updatePrice();
                });
            });

            // Слушаем изменения размеров
            const sizeOptions = document.querySelectorAll('.size-option');
            sizeOptions.forEach(option => {
                option.addEventListener('click', () => {
                    this.updatePrice();
                });
            });
        }

        validateForm() {
            const firstName = document.getElementById('order-first-name').value.trim();
            const lastName = document.getElementById('order-last-name').value.trim();
            const phone = document.getElementById('order-phone').value.trim();
            const email = document.getElementById('order-email').value.trim();
            const address = document.getElementById('order-address').value.trim();
            
            const isValid = firstName && lastName && phone && email && address;
            this.submitBtn.disabled = !isValid;
            
            return isValid;
        }

        updatePrice() {
            this.basePrice = this.getCurrentCardPrice();
            this.sizeAddition = this.getCurrentSizeAddition();
            const total = this.basePrice + this.sizeAddition;
            
            // Форматируем цены без лишних нулей
            document.getElementById('final-base-price').textContent = `$${this.formatPrice(this.basePrice)}`;
            document.getElementById('final-size-addition').textContent = `$${this.formatPrice(this.sizeAddition)}`;
            document.getElementById('final-total').textContent = `$${this.formatPrice(total)}`;
        }

        formatPrice(price) {
            const num = parseFloat(price);
            return num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
        }

        open() {
            this.popup.classList.add('active');
            document.body.style.overflow = 'hidden';
            this.updatePrice();
            this.validateForm();
        }

        close() {
            this.popup.classList.remove('active');
            document.body.style.overflow = '';
        }

        async submitOrder() {
            if (!this.validateForm()) {
                alert('Please fill in all required fields');
                return;
            }

            // Собрать данные
            const formData = {
                action: 'create_order',
                product: this.getCurrentCardName(),
                color: this.getCurrentCardColor(),
                size: this.getCurrentSizeName(),
                base_price: this.basePrice,
                size_addition: this.sizeAddition,
                final_total: this.basePrice + this.sizeAddition,
                first_name: document.getElementById('order-first-name').value.trim(),
                last_name: document.getElementById('order-last-name').value.trim(),
                phone: document.getElementById('order-phone').value.trim(),
                email: document.getElementById('order-email').value.trim(),
                address: document.getElementById('order-address').value.trim()
            };

            // Показать индикатор загрузки
            this.submitBtn.disabled = true;
            this.submitBtn.textContent = 'Processing...';

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(formData).toString()
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccessMessage(result.order_ref);
                } else {
                    throw new Error(result.message || 'Server error');
                }
            } catch (error) {
                console.error('Error submitting order:', error);
                alert('Sorry, there was an error processing your order. Please try again or contact us directly.');
                this.submitBtn.disabled = false;
                this.submitBtn.textContent = 'Complete Order';
            }
        }

        // И обновите showSuccessMessage:
        showSuccessMessage(orderReference) {
            this.popup.innerHTML = `
                <div class="order-popup-content">
                    <div class="order-popup-body" style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; color: #4ade80; margin-bottom: 20px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Order Submitted Successfully!</h2>
                        <p>Thank you for your order. We will contact you within 15 minutes to confirm the details.</p>
                        <p><strong>Order Reference: ${orderReference}</strong></p>
                        <button class="order-btn-submit" onclick="location.reload()" style="margin-top: 20px;">Close</button>
                    </div>
                </div>
            `;
        }
        /*
        showSuccessMessage() {
            this.popup.innerHTML = `
                <div class="order-popup-content">
                    <div class="order-popup-body" style="text-align: center; padding: 40px;">
                        <div style="font-size: 48px; color: #4ade80; margin-bottom: 20px;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2>Order Submitted Successfully!</h2>
                        <p>Thank you for your order. We will contact you within 15 minutes to confirm the details.</p>
                        <p><strong>Order Reference: RS${Date.now().toString().slice(-6)}</strong></p>
                        <button class="order-btn-submit" onclick="location.reload()" style="margin-top: 20px;">Close</button>
                    </div>
                </div>
            `;
        }*/
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', () => {
        new OrderPopup();
    });
    </script>

    <?php
    // Функция форматирования цены для PHP
    function formatPrice($price) {
        $num = floatval($price);
        return $num == intval($num) ? intval($num) : number_format($num, 2, '.', '');
    }
    include "../assets/brands.php";
    include "../assets/footer.php";
    ?>
</body>
</html>