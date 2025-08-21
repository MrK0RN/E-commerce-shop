<?php
include "../../system/db.php";
$product_id = (int)$_GET['product_id'];
$product = pgQuery("SELECT * FROM products WHERE id = $product_id")[0];
$images = pgQuery("SELECT * FROM product_images WHERE product_id = $product_id ORDER BY sort ASC;");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея — <?=$product['name']?></title>
    <link rel="stylesheet" href="css/products.css?v=<?=time()?>">
</head>
<body>
    <h1>Галерея: <?=htmlspecialchars($product['name'])?></h1>
    <form action="upload_image.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?=$product_id?>">
        <input type="file" name="images[]" multiple>
        <button type="submit">Загрузить изображения</button>
    </form>
    <div class="gallery-list">
        <?php foreach ($images as $img): ?>
            <div class="gallery-item">
                <img src="<?=htmlspecialchars($img['url'])?>" alt="">
                <button class="delete-btn" onclick="window.location.href='delete_image.php?id=<?=$img['id']?>&product_id=<?=$product_id?>'">Удалить</button>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
