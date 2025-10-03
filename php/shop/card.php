<?php
$id = $_GET["id"];
$card_id = $_GET["card"];
include "../system/db.php";
$cards = pgQuery('SELECT * FROM cards where good_id = '.$id.';');
if (empty($card_id)){
    $card_id = $cards[0]["id"];
}

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
                
                <div class="warranty-rating">
                    <div class="warranty"><i class="fas fa-shield-alt"></i> Warranty: 5 years</div>
                    <div class="rating"><i class="fas fa-star"></i> <?=$good["score"]?> (247 reviews)</div>
                </div>
                
                <div class="price">
                    <div class="old-price">$<?=$good["old_price"]?></div>
                    <div class="new-price" id="current-price">$<?=$card["price"]?> <span class="discount">-18%</span></div>
                    <div class="price-note">Financing available from $249/month</div>
                </div>
                
                <div class="action-buttons">
                    <button class="order-btn"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                    <button class="wishlist-btn tooltip" data-tooltip="Add to Wishlist"><i class="fas fa-heart"></i></button>
                </div>
            </div>
        </div>
        
        <div class="products-carousel">
            <div class="section-title">Frequently Bought Together <i class="fas fa-arrow-right"></i></div>
            <div class="carousel-container">
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-156 7771024-8ce2d0f6 6c6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Accessory 1">
                    <div class="carousel-item-content">
                        <h3>Automation Kit</h3>
                        <div class="rating">★ ★ ★ ★ ★ <span>(4.9)</span></div>
                        <div class="price">
                            <span class="new-price">$1,290</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-158 9658319723- 4c5d4b 96d6d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Accessory 2">
                    <div class="carousel-item-content">
                        <h3>Keypad Entry System</h3>
                        <div class="rating">★ ★ ★ ★ ☆ <span>(4.5)</span></div>
                        <div class="price">
                            <span class="new-price">$449</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-161 1746869696-89 8742a6c554f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Accessory 3">
                    <div class="carousel-item-content">
                        <h3>Remote Controls (Set of 2)</h3>
                        <div class="rating">★ ★ ★ ★ ★ <span>(4.8)</span></div>
                        <div class="price">
                            <span class="new-price">$129</span>
                            <span class="old-price">$149</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-161 5875441446-0f8f5b4f92a5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Accessory 4">
                    <div class="carousel-item-content">
                        <h3>Professional Installation Kit</h3>
                        <div class="rating">★ ★ ★ ★ ☆ <span>(4.6)</span></div>
                        <div class="price">
                            <span class="new-price">$299</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="products-carousel">
            <div class="section-title">Related Products <i class="fas fa-arrow-right"></i></div>
            <div class="carousel-container">
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-159 9658319723- 4c5d4b 96d6d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Product 2">
                    <div class="carousel-item-content">
                        <h3>Commercial Sliding Gate</h3>
                        <div class="rating">★ ★ ★ ★ ☆ <span>(4.7)</span></div>
                        <div class="price">
                            <span class="new-price">$5,990</span>
                            <span class="old-price">$6,490</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-156 7771024-8ce2d0f6 6c6e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Product 3">
                    <div class="carousel-item-content">
                        <h3>Residential Security Gate</h3>
                        <div class="rating">★ ★ ★ ★ ★ <span>(4.9)</span></div>
                        <div class="price">
                            <span class="new-price">$4,290</span>
                            <span class="old-price">$4,790</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-158 9658319723- 4c5d4b 96d6d7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Product 4">
                    <div class="carousel-item-content">
                        <h3>Barrier Arm System</h3>
                        <div class="rating">★ ★ ★ ★ ☆ <span>(4.6)</span></div>
                        <div class="price">
                            <span class="new-price">$3,499</span>
                            <span class="old-price">$3,899</span>
                        </div>
                    </div>
                </div>
                
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-161 1746869696-89 8742a6c554f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=300&h=200&q=80" alt="Product 5">
                    <div class="carousel-item-content">
                        <h3>Perimeter Fencing</h3>
                        <div class="rating">★ ★ ★ ★ ★ <span>(4.8)</span></div>
                        <div class="price">
                            <span class="new-price">$24.99/m</span>
                            <span class="old-price">$29.99/m</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="carousel-controls">
                <button class="carousel-btn prev"><i class="fas fa-chevron-left"></i></button>
                <button class="carousel-btn next"><i class="fas fa-chevron-right"></i></button>
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
                if (price && currentPrice){
                    const discountHTML = currentPrice.querySelector('.discount')?.outerHTML || '';
                    currentPrice.innerHTML = `$${price} ${discountHTML}`.trim();
                }

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
        
        // Обработка кнопки заказа
        const orderBtn = document.querySelector('.order-btn');
        
        orderBtn.addEventListener('click', function() {
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
        
        // Карусель товаров
        const carouselNext = document.querySelector('.carousel-controls .next');
        const carouselPrev = document.querySelector('.carousel-controls .prev');
        const carousel = document.querySelector('.carousel-container');
        
        carouselNext.addEventListener('click', () => {
            carousel.scrollBy({ left: 300, behavior: 'smooth' });
        });
        
        carouselPrev.addEventListener('click', () => {
            carousel.scrollBy({ left: -300, behavior: 'smooth' });
        });
    </script>
    <!-- Добавьте этот код в card.php после существующего JavaScript -->

<!-- Стили для pop-up -->
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
    max-width: 800px;
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
    min-height: 500px;
}

/* Прогресс-бар */
.order-progress-section {
    padding: 0 0 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid #eaeaea;
}

.order-progress-label {
    font-size: 1rem;
    color: #374151;
    margin-bottom: 12px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-question-counter {
    font-size: 0.9rem;
    color: #6b7280;
    font-weight: 500;
}

.order-progress-container {
    width: 100%;
    background-color: #f0f0f0;
    border-radius: 12px;
    overflow: hidden;
    height: 12px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
}

.order-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #86efac, #4ade80);
    border-radius: 12px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    width: 33.33%;
}

/* Секция вопроса */
.order-question-section {
    margin-bottom: 30px;
}

.order-question-section h3 {
    font-size: 1.5rem;
    color: #1f2937;
    margin-bottom: 20px;
    font-weight: 500;
    line-height: 1.4;
}

/* Стили для размеров */
.size-inputs {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.size-input {
    display: flex;
    flex-direction: column;
}

.size-input label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
}

.size-input input {
    padding: 12px 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease;
}

.size-input input:focus {
    outline: none;
    border-color: #4ade80;
}

.size-unit {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 5px;
}

/* Стили для дополнительных товаров */
.addon-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.addon-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background-color: white;
    position: relative;
}

.addon-card:hover {
    border-color: #86efac;
    background-color: #f0fdf4;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.addon-card.selected {
    border-color: #4ade80;
    background-color: #f0fdf4;
    box-shadow: 0 4px 16px rgba(74, 222, 128, 0.15);
}

.addon-card.selected::after {
    content: '✓';
    position: absolute;
    top: 8px;
    right: 8px;
    width: 20px;
    height: 20px;
    background: #4ade80;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.addon-info h4 {
    margin: 0 0 8px 0;
    color: #1f2937;
    font-size: 1.1rem;
}

.addon-info p {
    margin: 0 0 10px 0;
    color: #6b7280;
    font-size: 0.9rem;
}

.addon-price {
    font-weight: 600;
    color: #4ade80;
    font-size: 1.1rem;
}

/* Стили для комментария и контактов */
.comment-section textarea {
    width: 100%;
    padding: 15px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 16px;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
    margin-bottom: 20px;
}

.comment-section textarea:focus {
    outline: none;
    border-color: #4ade80;
}

.contact-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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

/* Итоговая сумма */
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

/* Навигация */
.order-navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
}

.order-btn {
    padding: 14px 28px;
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
    min-width: 120px;
}

.order-btn-back {
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.order-btn-back:hover {
    background-color: #e5e7eb;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-btn-next {
    background: linear-gradient(135deg, #86efac, #4ade80);
    color: white;
    box-shadow: 0 2px 8px rgba(74, 222, 128, 0.3);
}

.order-btn-next:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74, 222, 128, 0.4);
}

.order-btn-submit {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: white;
    box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
}

.order-btn-submit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
}

.order-btn:disabled {
    background: #9ca3af;
    box-shadow: none;
    transform: none;
    cursor: not-allowed;
}

/* Анимации */
.order-question-section {
    transition: opacity 0.3s ease;
}

.order-question-section.fade-out {
    opacity: 0;
}

/* Адаптивность */
@media (max-width: 768px) {
    .order-popup-content {
        margin: 20px;
        width: calc(100% - 40px);
    }
    
    .order-popup-body {
        padding: 20px;
    }
    
    .size-inputs,
    .contact-fields {
        grid-template-columns: 1fr;
    }
    
    .addon-options {
        grid-template-columns: 1fr;
    }
    
    .order-navigation {
        flex-direction: column;
        gap: 15px;
    }
    
    .order-btn {
        width: 100%;
    }
}
/* Стили для кнопки пропуска размеров */
.skip-dimensions {
    text-align: center;
    margin: 20px 0;
}

.skip-dimensions-btn {
    background: transparent;
    border: 2px dashed #6b7280;
    color: #6b7280;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.skip-dimensions-btn:hover {
    border-color: #4ade80;
    color: #4ade80;
    background-color: #f0fdf4;
    transform: translateY(-1px);
}

.skip-dimensions-btn:active {
    transform: translateY(0);
}

/* Индикатор что размеры не указаны */
.dimensions-skipped .size-inputs {
    opacity: 0.5;
    pointer-events: none;
}

.dimensions-skipped .skip-dimensions-btn {
    background: #f0fdf4;
    border-color: #4ade80;
    color: #4ade80;
    border-style: solid;
}
</style>

<!-- Pop-up форма -->
<div class="order-popup" id="orderPopup">
    <div class="order-popup-content">
        <button class="order-popup-close" id="orderClose">&times;</button>
        
        <div class="order-popup-body">
            <!-- Прогресс-бар -->
            <div class="order-progress-section">
                <div class="order-progress-label">
                    <span>Step</span>
                    <span class="order-question-counter" id="orderQuestionCounter">1 of 3</span>
                </div>
                <div class="order-progress-container">
                    <div class="order-progress-bar" id="orderProgressBar"></div>
                </div>
            </div>

            <!-- Вопрос 1: Размеры -->
            <div class="order-question-section" id="question1">
                <h3>Enter the dimensions for your order</h3>
                
                <div class="size-inputs">
                    <div class="size-input">
                        <label for="order-width">Width</label>
                        <input type="number" id="order-width" placeholder="0.0" step="0.1" min="0">
                        <div class="size-unit">meters</div>
                    </div>
                    
                    <div class="size-input">
                        <label for="order-height">Height</label>
                        <input type="number" id="order-height" placeholder="0.0" step="0.1" min="0">
                        <div class="size-unit">meters</div>
                    </div>
                </div>
                
                <!-- Кнопка пропуска размеров -->
                <div class="skip-dimensions">
                    <button class="skip-dimensions-btn" id="skipDimensions">
                        <i class="fas fa-question-circle"></i> I don't know dimensions
                    </button>
                </div>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Base price:</span>
                        <span id="base-price">$<?=$card["price"]?></span>
                    </div>
                    <div class="summary-row">
                        <span>Size adjustment:</span>
                        <span id="size-adjustment">$0</span>
                    </div>
                    <div class="summary-total">
                        <span>Current total:</span>
                        <span id="current-total">$<?=$card["price"]?></span>
                    </div>
                </div>
            </div>

            <!-- Вопрос 2: Дополнительные товары -->
            <div class="order-question-section" id="question2" style="display: none;">
                <h3>Select additional products</h3>
                
                <div class="addon-options">
                    <div class="addon-card" data-price="1290">
                        <div class="addon-info">
                            <h4>Automation Kit</h4>
                            <p>Complete automation system for remote control</p>
                            <div class="addon-price">+$1,290</div>
                        </div>
                    </div>
                    
                    <div class="addon-card" data-price="449">
                        <div class="addon-info">
                            <h4>Keypad Entry System</h4>
                            <p>Secure keypad access control</p>
                            <div class="addon-price">+$449</div>
                        </div>
                    </div>
                    
                    <div class="addon-card" data-price="129">
                        <div class="addon-info">
                            <h4>Remote Controls (Set of 2)</h4>
                            <p>Additional remote controls for convenience</p>
                            <div class="addon-price">+$129</div>
                        </div>
                    </div>
                    
                    <div class="addon-card" data-price="299">
                        <div class="addon-info">
                            <h4>Professional Installation Kit</h4>
                            <p>All necessary hardware for installation</p>
                            <div class="addon-price">+$299</div>
                        </div>
                    </div>
                </div>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Base price:</span>
                        <span id="summary-base-price">$<?=$card["price"]?></span>
                    </div>
                    <div class="summary-row">
                        <span>Size adjustment:</span>
                        <span id="summary-size-adjustment">$0</span>
                    </div>
                    <div class="summary-row">
                        <span>Additional products:</span>
                        <span id="addons-total">$0</span>
                    </div>
                    <div class="summary-total">
                        <span>Current total:</span>
                        <span id="summary-current-total">$<?=$card["price"]?></span>
                    </div>
                </div>
            </div>

            <!-- Вопрос 3: Комментарий и контакты -->
            <div class="order-question-section" id="question3" style="display: none;">
                <h3>Final details and contact information</h3>
                
                <div class="comment-section">
                    <label for="order-comment">Additional comments or special requirements</label>
                    <textarea id="order-comment" placeholder="Any special instructions or requirements for your order..."></textarea>
                </div>
                
                <div class="contact-fields">
                    <div class="contact-field">
                        <label for="order-name">Your Name *</label>
                        <input type="text" id="order-name" placeholder="John Smith" required>
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
                        <label for="order-address">Delivery Address</label>
                        <input type="text" id="order-address" placeholder="Your complete address">
                    </div>
                </div>
                
                <div class="order-summary">
                    <div class="summary-row">
                        <span>Base price:</span>
                        <span id="final-base-price">$<?=$card["price"]?></span>
                    </div>
                    <div class="summary-row">
                        <span>Size adjustment:</span>
                        <span id="final-size-adjustment">$0</span>
                    </div>
                    <div class="summary-row">
                        <span>Additional products:</span>
                        <span id="final-addons-total">$0</span>
                    </div>
                    <div class="summary-total">
                        <span>Final total:</span>
                        <span id="final-total">$<?=$card["price"]?></span>
                    </div>
                </div>
            </div>

            <!-- Навигация -->
            <div class="order-navigation">
                <button class="order-btn order-btn-back" id="orderBack">Back</button>
                <button class="order-btn order-btn-next" id="orderNext">Next</button>
                <button class="order-btn order-btn-submit" id="orderSubmit" style="display: none;">Complete Order</button>
            </div>
        </div>
    </div>
</div>
<?php
function onlyDigits($string) {
    return preg_replace('/[^0-9]/', '', $string);
}
?>
<script>
// Класс для управления pop-up заказа
// Класс для управления pop-up заказа
// Класс для управления pop-up заказа
// Класс для управления pop-up заказа
function onlyDigitsWithDecimal(str) {
    return str.replace(/[^\d.]/g, '');
}
class OrderPopup {
    constructor() {
        this.popup = document.getElementById('orderPopup');
        this.openBtn = document.querySelector('.order-btn');
        this.closeBtn = document.getElementById('orderClose');
        this.nextBtn = document.getElementById('orderNext');
        this.backBtn = document.getElementById('orderBack');
        this.submitBtn = document.getElementById('orderSubmit');
        this.progressBar = document.getElementById('orderProgressBar');
        this.questionCounter = document.getElementById('orderQuestionCounter');
        this.skipDimensionsBtn = document.getElementById('skipDimensions');
        
        this.currentStep = 1;
        this.totalSteps = 3;
        this.basePrice = this.getCurrentCardPrice(); // Получаем текущую цену
        this.sizeAdjustment = 0;
        this.addonsTotal = 0;
        this.dimensionsSkipped = false;
        
        this.init();
    }
    
    // Метод для получения текущей цены карточки
    getCurrentCardPrice() {
        const activeColorOption = document.querySelector('.color-option.active');
        if (activeColorOption) {
            const price = activeColorOption.getAttribute('data-price');
            return parseInt(onlyDigitsWithDecimal(price)) || <?=onlyDigits($card["price"])?>;
        }
        return <?=onlyDigits($card["price"])?>;
    }

    // Метод для получения текущего цвета
    getCurrentCardColor() {
        const activeColorOption = document.querySelector('.color-option.active');
        return activeColorOption ? activeColorOption.getAttribute('data-color') : '<?=$card["color"]?>';
    }

    // Метод для получения текущего названия карточки
    getCurrentCardName() {
        return '<?=$good["name"]?>';
    }

    init() {
        // Открытие pop-up при клике на кнопку "Ready to go ahead"
        if (this.openBtn) {
            this.openBtn.addEventListener('click', (e) => {
                e.preventDefault();
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

        // Навигация
        this.nextBtn.addEventListener('click', () => {
            this.goToNextStep();
        });

        this.backBtn.addEventListener('click', () => {
            this.goToPreviousStep();
        });

        // Отправка формы
        this.submitBtn.addEventListener('click', () => {
            this.submitOrder();
        });

        // Обработчики для размеров
        document.getElementById('order-width').addEventListener('input', () => {
            this.calculateSizeAdjustment();
        });

        document.getElementById('order-height').addEventListener('input', () => {
            this.calculateSizeAdjustment();
        });

        // Обработчик для кнопки пропуска размеров
        this.skipDimensionsBtn.addEventListener('click', () => {
            this.skipDimensions();
        });

        // Обработчики для дополнительных товаров
        document.querySelectorAll('.addon-card').forEach(card => {
            card.addEventListener('click', () => {
                card.classList.toggle('selected');
                this.calculateAddonsTotal();
            });
        });

        // Закрытие по ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.popup.classList.contains('active')) {
                this.close();
            }
        });

        // Слушаем изменения цветовых вариантов
        this.setupColorChangeListener();
    }

    // Настройка слушателя изменений цветовых вариантов
    setupColorChangeListener() {
        const colorOptions = document.querySelectorAll('.color-option');
        colorOptions.forEach(option => {
            option.addEventListener('click', () => {
                // Обновляем базовую цену при изменении цвета
                this.basePrice = this.getCurrentCardPrice();
                this.updateAllSummaries();
            });
        });
    }

    open() {
        this.popup.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Обновляем базовую цену при открытии pop-up
        this.basePrice = this.getCurrentCardPrice();
        this.resetForm();
    }

    close() {
        this.popup.classList.remove('active');
        document.body.style.overflow = '';
    }

    resetForm() {
        this.currentStep = 1;
        this.sizeAdjustment = 0;
        this.addonsTotal = 0;
        this.dimensionsSkipped = false;
        this.updateProgress();
        this.showStep(1);
        this.updateAllSummaries();
        
        // Сброс формы
        document.getElementById('order-width').value = '';
        document.getElementById('order-height').value = '';
        document.getElementById('question1').classList.remove('dimensions-skipped');
        this.skipDimensionsBtn.innerHTML = '<i class="fas fa-question-circle"></i> I don\'t know dimensions';
        document.querySelectorAll('.addon-card').forEach(card => card.classList.remove('selected'));
        document.getElementById('order-comment').value = '';
        document.getElementById('order-name').value = '';
        document.getElementById('order-phone').value = '';
        document.getElementById('order-email').value = '';
        document.getElementById('order-address').value = '';
    }

    updateProgress() {
        const progressPercentage = (this.currentStep / this.totalSteps) * 100;
        this.progressBar.style.width = `${progressPercentage}%`;
        this.questionCounter.textContent = `${this.currentStep} of ${this.totalSteps}`;
    }

    showStep(step) {
        // Скрыть все шаги
        document.querySelectorAll('.order-question-section').forEach(section => {
            section.style.display = 'none';
        });

        // Показать текущий шаг
        document.getElementById(`question${step}`).style.display = 'block';

        // Обновить кнопки навигации
        this.backBtn.style.display = step > 1 ? 'block' : 'none';
        this.nextBtn.style.display = step < this.totalSteps ? 'block' : 'none';
        this.submitBtn.style.display = step === this.totalSteps ? 'block' : 'none';
    }

    goToNextStep() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateProgress();
            this.showStep(this.currentStep);
            this.updateAllSummaries();
        }
    }

    goToPreviousStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateProgress();
            this.showStep(this.currentStep);
            this.updateAllSummaries();
        }
    }

    skipDimensions() {
        this.dimensionsSkipped = true;
        
        // Отключаем поля ввода и устанавливаем значения по умолчанию
        document.getElementById('order-width').value = '';
        document.getElementById('order-height').value = '';
        document.getElementById('question1').classList.add('dimensions-skipped');
        this.skipDimensionsBtn.innerHTML = '<i class="fas fa-check-circle"></i> Dimensions skipped';
        this.sizeAdjustment = 0;
        
        this.updateAllSummaries();
        
        // Автоматически переходим к следующему вопросу через короткую задержку
        setTimeout(() => {
            this.goToNextStep();
        }, 300);
    }

    calculateSizeAdjustment() {
        // Если размеры пропущены, не рассчитываем
        if (this.dimensionsSkipped) {
            this.sizeAdjustment = 0;
            this.updateAllSummaries();
            return;
        }

        const width = parseFloat(document.getElementById('order-width').value) || 0;
        const height = parseFloat(document.getElementById('order-height').value) || 0;
        
        // Простая логика расчета: $100 за квадратный метр сверх базового размера
        const baseArea = 2; // базовый размер 2 кв.м.
        const area = width * height;
        const extraArea = Math.max(0, area - baseArea);
        this.sizeAdjustment = Math.round(extraArea * 100);
        
        this.updateAllSummaries();
    }

    calculateAddonsTotal() {
        this.addonsTotal = 0;
        document.querySelectorAll('.addon-card.selected').forEach(card => {
            const price = parseInt(card.getAttribute('data-price'));
            this.addonsTotal += price;
        });
        
        this.updateAllSummaries();
    }

    updateAllSummaries() {
        const total = this.basePrice + this.sizeAdjustment + this.addonsTotal;
        
        // Обновить все блоки с итогами
        document.getElementById('size-adjustment').textContent = `$${this.sizeAdjustment}`;
        document.getElementById('current-total').textContent = `$${total}`;
        
        document.getElementById('summary-base-price').textContent = `$${this.basePrice}`;
        document.getElementById('summary-size-adjustment').textContent = `$${this.sizeAdjustment}`;
        document.getElementById('addons-total').textContent = `$${this.addonsTotal}`;
        document.getElementById('summary-current-total').textContent = `$${total}`;
        
        document.getElementById('final-base-price').textContent = `$${this.basePrice}`;
        document.getElementById('final-size-adjustment').textContent = `$${this.sizeAdjustment}`;
        document.getElementById('final-addons-total').textContent = `$${this.addonsTotal}`;
        document.getElementById('final-total').textContent = `$${total}`;
    }

    async submitOrder() {
        // Валидация формы
        if (!this.validateForm()) {
            alert('Please fill in all required fields');
            return;
        }

        // Собрать данные
        const formData = {
            product: this.getCurrentCardName(),
            color: this.getCurrentCardColor(),
            base_price: this.basePrice,
            size_adjustment: this.sizeAdjustment,
            addons_total: this.addonsTotal,
            final_total: this.basePrice + this.sizeAdjustment + this.addonsTotal,
            dimensions_skipped: this.dimensionsSkipped,
            width: this.dimensionsSkipped ? 'Not specified' : document.getElementById('order-width').value,
            height: this.dimensionsSkipped ? 'Not specified' : document.getElementById('order-height').value,
            addons: Array.from(document.querySelectorAll('.addon-card.selected')).map(card => ({
                name: card.querySelector('h4').textContent,
                price: card.getAttribute('data-price')
            })),
            comment: document.getElementById('order-comment').value,
            name: document.getElementById('order-name').value,
            phone: document.getElementById('order-phone').value,
            email: document.getElementById('order-email').value,
            address: document.getElementById('order-address').value,
            timestamp: new Date().toISOString()
        };

        // Показать индикатор загрузки
        this.submitBtn.disabled = true;
        this.submitBtn.textContent = 'Processing...';

        try {
            const response = await fetch('http://localhost:8900/mail/api', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                this.showSuccessMessage();
            } else {
                throw new Error('Server error');
            }
        } catch (error) {
            console.error('Error submitting order:', error);
            alert('Sorry, there was an error processing your order. Please try again or contact us directly.');
            this.submitBtn.disabled = false;
            this.submitBtn.textContent = 'Complete Order';
        }
    }

    validateForm() {
        const name = document.getElementById('order-name').value.trim();
        const phone = document.getElementById('order-phone').value.trim();
        const email = document.getElementById('order-email').value.trim();
        
        return name && phone && email;
    }

    showSuccessMessage() {
        this.popup.innerHTML = `
            <div class="order-popup-content">
                <div class="order-popup-body" style="text-align: center; padding: 40px;">
                    <div class="success-checkmark">
                        <div class="check-icon">
                            <span class="icon-line line-tip"></span>
                            <span class="icon-line line-long"></span>
                            <div class="icon-circle"></div>
                        </div>
                    </div>
                    <h2>Order Submitted Successfully!</h2>
                    <p>Thank you for your order. We will contact you within 15 minutes to confirm the details.</p>
                    <p><strong>Order Reference: RS${Date.now().toString().slice(-6)}</strong></p>
                    <button class="order-btn order-btn-submit" onclick="location.reload()" style="margin-top: 20px;">Close</button>
                </div>
            </div>
        `;
    }
}
// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    new OrderPopup();
    
    // Изменить текст кнопки на "Ready to go ahead"
    const orderBtn = document.querySelector('.order-btn');
    if (orderBtn) {
        orderBtn.innerHTML = '<i class="fas fa-rocket"></i> Ready to go ahead';
    }
});

</script>
    <?php
	include "../assets/brands.php";
	include "../assets/footer.php";
	?>
</body>
</html>