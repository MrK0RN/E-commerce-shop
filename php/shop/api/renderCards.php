<?php

include "../../system/db.php";

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


$dir = __DIR__ . '/../images/'.$id; // Укажи путь к нужной папке
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

?>
<input type="hidden" id="fileCount_<?=$id?>" value='<?=$fileList?>'>

<div class="image-container" id="imageContainer-<?=$id?>"></div>
<div class="title"><?=$name?></div>
    <div class="rating">
        <span class="stars"><?=$stars?></span>
        <span><?=$score?></span>
    </div>

    <div class="colors">
        <div class="color" style="background:#ffffff" title="Белый"></div>
        <div class="color" style="background:#2b2b2b" title="Коричневый"></div>
        <div class="color" style="background:#7b7b7b" title="Серый"></div>
        <span class="more">+ ещё 7 цветов</span>
    </div>

    <div class="prices">
        <span class="old"><?=$old_price?> <?=$currency?></span>
        <span class="current"><?=$price?> <?=$currency?></span>
    </div>

    <div class="delivery">Доставка: <?=$delivery?></div>