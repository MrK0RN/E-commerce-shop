<?php

include "../system/db.php";

$goods = pgQuery("SELECT * FROM goods;");

foreach ($goods as $good){
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
                        <ul class="params">
                            <li>шириной до 11 800 мм</li>
                            <li>высотой до 6 300 мм</li>
                            <li>площадью до 50 м²</li>
                        </ul>
                    </div>
                    
                    <div class="options-group">
                        <div class="options__title">Опции:</div>
                        <div class="option checked">
                            <div>стальной сплошной профиль</div>
                        </div>
                        <div class="option checked">
                            <div>стальной перфорированный профиль</div>
                        </div>
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
                    <button class="actions-button inverse" onclick="calculatePrice()">
                        Рассчитать
                    </button>
                </div>
            </div>
        </div>
    </div>';

}

?>