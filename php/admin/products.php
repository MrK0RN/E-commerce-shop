<?php
include "../system/db.php";
$responce = pgQuery("SELECT * FROM products ORDER BY id DESC;");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Товары — Галерея</title>
    <link rel="stylesheet" href="css/products.css?v<?=time()?>">
</head>
<body>
    <div class="products-header">
        <h1>Товары</h1>
        <button onclick="window.location.href='assets/product_form.php'">Добавить товар</button>
        <input type="text" id="search" placeholder="Поиск по названию...">
    </div>
    <div class="products-gallery">
        <?php foreach ($responce as $product): ?>
            <div class="product-card">
                <div class="product-thumb">
                    <img src="<?=htmlspecialchars($product['main_image'] ?? 'assets/img/no-image.png')?>" alt="">
                </div>
                <div class="product-info">
                    <h2><?=htmlspecialchars($product['name'])?></h2>
                    <div class="product-price"><?=$product['price']?> ₽</div>
                    <div class="product-actions">
                        <button onclick="window.location.href='assets/product_form.php?edit_id=<?=$product['id']?>'">Редактировать</button>
                        <button onclick="window.location.href='assets/product_gallery.php?product_id=<?=$product['id']?>'">Галерея</button>
                        <button class="delete-btn" onclick="window.location.href='assets/delete.php?table_name=products&id=<?=$product['id']?>'">Удалить</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
