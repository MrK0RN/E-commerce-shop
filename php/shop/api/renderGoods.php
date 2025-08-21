<?php

include "../system/db.php";

$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$amount = isset($_GET['amount']) ? intval($_GET['amount']) : 10; // fallback default

$goods = pgQuery("SELECT * FROM goods ORDER BY id ASC LIMIT $amount OFFSET $offset;");

foreach ($goods as $good){
    $params = pgQuery("SELECT * FROM more WHERE id = ".$good["id"]." AND more = 'params';");
    $options = pgQuery("SELECT * FROM more WHERE id = ".$good["id"]." AND more = 'options';");
    $text2 = '
    <div class="element-item">
        <div class="basic-wrapper">
            <div class="element-item__block-image">
                <div class="picture">
                    <img src="'.$good["src_img"].'" alt="'.$good["name"].'">
                </div>
            </div>
            
            <div class="element-item__block-param">
                <div class="title">'.$good["src_img"].'</div>
                
                <div class="params-and-options">
                    <div class="params-group">
                        <div class="params-header">Характеристики:</div>
                        <ul class="params">';
    foreach ($params as $param){
        $text2 .= "<li>".$param."</li>";
    }
    $text2 .= '</ul>
                    </div>
                    
                    <div class="options-group">
                        <div class="options__title">Опции:</div>';
    foreach ($options as $option){
        $text2 .= '<div class="option checked">
                            <div>'.$option.'</div>
                        </div>';
    }
    $text2 .= '
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="element-item__block-trade">
            <div class="price">
                <div class="price__title">Цена:</div>
                <div class="price__value">от '.$good["price"].'</div>
            </div>
            
            <div class="actions">
                <div class="actions__wrapper">
                    <a class="actions-button" href="#details">Подробнее</a>
                    <button class="actions-button inverse">
                        Рассчитать
                    </button>
                </div>
            </div>
        </div>
    </div>';

}

?>