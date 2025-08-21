<?php

$main = '
<div class="products-header">
    <button class="add-btn" onclick="window.location.href=\'assets/product_form.php\'">Добавить товар</button>
    <input type="text" id="search" class="search-box" placeholder="Поиск по названию...">
</div>
<div class="products-gallery">';
foreach ($responce as $product){
    $main .= '
        <div class="product-card">
            <div class="product-thumb">
                <img src="'.
                htmlspecialchars($product['main_image'] ?? 'assets/img/no-image.png').'" alt="">
            </div>
            <div class="product-info">
                <h2>'.htmlspecialchars($product['name']).'</h2>
                <div class="product-price">'.$product['price'].' ₽</div>
                <div class="product-actions">
                    <button onclick="window.location.href=\'assets/product_form.php?edit_id='.$product['id'].'\'">Редактировать</button>
                    <button onclick="window.location.href=\'assets/product_gallery.php?product_id='.$product['id'].'\'">Галерея</button>
                    <button class="delete-btn" onclick="window.location.href=\'assets/delete.php?table_name=products&id='.$product['id'].'\'">Удалить</button>
                </div>
            </div>
        </div>';
    }

$main .= '</div>';
?>