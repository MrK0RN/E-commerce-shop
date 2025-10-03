<?php

include "../../system/db.php";

$colors_amount = 3;
$currency = "₽";

$id = $_GET['id'];

$responces = pgQuery('SELECT * FROM goods WHERE id = '.$id);
$good = $responces[0];

$name = $good["name"];
$score = round($good["score"]*10)/10;
$stars = "";
for ($i = 1; $i < 6; $i++){
    if ($i >= $score){
        $stars .= "☆";
    } else {
        $stars .= "★";
    }
}
$old_price = $good["old_price"];
$price = $good["price"];
$delivery = $good["delivery"];


$dir = __DIR__ . '/../../data/images/'.$id; // Укажи путь к нужной папке
$fileList = "";

if (is_dir($dir)) {
    $files = array_diff(scandir($dir), ['.', '..']);
    $fileNames = [];
    
    foreach ($files as $file) {
        if (is_file($dir . '/' . $file)) {
            $fileNames[] = $file;
        }
    }
    
    $fileList = implode('|;', $fileNames);
}


$colors = pgQuery("SELECT * FROM cards WHERE good_id = ".$id);
?>
<input type="hidden" id="fileCount_<?=$id?>" value='<?=$fileList?>'>

<div class="image-container" id="imageContainer-<?=$id?>"></div>
<div class="title"><?=$name?></div>
    <div class="rating">
        <span class="stars"><?=$stars?></span>
        <span><?=$score?></span>
    </div>

    <div class="colors">
        <?php
        if (count($colors) > $colors_amount){
            for ($i = 0; $i < $colors_amount; $i++){
                echo '<div class="color" style="background:'.$colors[$i]['color'].'" title="Белый"></div>';
            }
            echo '<span class="more">+ ещё '.(count($colors) - $colors_amount).' цветов</span>';
        } else {
            foreach ($colors as $color){
                echo '<div class="color" style="background:'.$color['color'].'" title="Белый"></div>';
            }
        }
            
        ?>
    </div>

    <div class="prices">
        <span class="old"><?=$old_price?> <?=$currency?></span>
        <span class="current"><?=$price?> <?=$currency?></span>
    </div>

    <div class="delivery">Доставка: <?=$delivery?></div>