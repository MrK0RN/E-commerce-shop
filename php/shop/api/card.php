<?php
$id = $_GET["id"];
$card_id = $_GET["card"] ?? 1;
include "../../system/db.php";
// Вспомогательная функция получения списка изображений для карточки
function getCardImages($cardId) {
    $cardImagesPath = "../../data/cards/" . $cardId;
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
$cards = pgQuery('SELECT * FROM cards where good_id = '.$id.';');
$params = pgQuery("SELECT * FROM params WHERE good_id = ".$good["id"].";");
$options = pgQuery("SELECT * FROM options WHERE good_id = ".$good["id"].";");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профессиональная карточка товара</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/cards.css?v=1">
</head>
<body>
    <?php
    include "../../assets/header_black.php";?>
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

    <footer>
        <div class="container">
            <p>© 2023 GateSolutions. All rights reserved.</p>
        </div>
    </footer>

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
        const apiImages = '../api/card.php?action=images';
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
            this.innerHTML = '<i class="fas fa-check"></i> Добавлено в корзину';
            this.style.background = 'var(--success)';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-shopping-cart"></i> Добавить в корзину';
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
</body>
</html>