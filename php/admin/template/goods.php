<?php

$main = '
<div class="products-header">
    <button class="add-btn" onclick="window.location.href=\'assets/add_goods.php\'">Добавить товар</button>
    <input type="text" id="search" class="search-box" placeholder="Поиск по названию...">
</div>
<div class="products-gallery">';


$main .= '</div>';
$table_name = "goods";
$add = false;
$edit = true;
$delete = true;
$buttons = ["Редактировать фото" => "assets/add_photos.php"];
include "assets/tables.php";
$main .= $text2;
?>