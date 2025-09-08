<?php

include "../system/db.php";

$goods = pgQuery("SELECT * FROM goods;");
foreach ($goods as $good){

    $params = pgQuery("SELECT * FROM params WHERE good_id = ".$good["id"].";");
    $options = pgQuery("SELECT * FROM options WHERE good_id = ".$good["id"].";");
    $text2 = '
    <div class="element-item">
        <div class="basic-wrapper">
            <div class="element-item__block-image">
                <div class="picture">
                    <img src="'.$good["src_img"].'" alt="'.$good["name"].'">
                </div>
            </div>
            
            <div class="element-item__block-param">
                <div class="title">'.$good["name"].'</div>
                
                <div class="params-and-options">
                    <div class="params-group">
                        <div class="params-header">Характеристики:</div>
                        <ul class="params">';

    foreach ($params as $param){
        $text2 .= '<li>'.$param["text"].'</li>';
    }

    $text2 .= '
                        </ul>
                    </div>
                    
                    <div class="options-group">
                        <div class="options__title">Options:</div>';
    foreach ($options as $option){
        $text2 .= '<div class="option checked"><div>'.$option["text"].'</div></div>';
    }

    $text2 .= '
                    </div>
                </div>
            </div>
            
        </div>
        <div class="color-variants">
        <!-- <div class="ml-10"> -->
            <div class="color-pause"></div>
            <div class="color-option selected" style="background-color: #000000;" title="Чёрный"></div>
            <div class="color-option" style="background-color: #ffffff; border: 1px solid #ddd;" title="Белый"></div>
            <div class="color-option" style="background-color: #c0c0c0;" title="Серебристый"></div>
            <div class="color-option" style="background-color: #ff6b6b;" title="Красный"></div>
            <div class="color-option" style="background-color: #4ecdc4;" title="Бирюзовый"></div>
            <div class="color-option" style="background-color: #45b7d1;" title="Голубой"></div>
        <!-- </div> -->
        </div>
        <div class="element-item__block-trade">
            <div class="price">
                <div class="price__title">Цена:</div>
                <div class="price__value">от '.$good["price"].'</div>
            </div>';
    $text2 .= "<script>
    // Скрипт для выбора цвета
    document.querySelectorAll('.color-option').forEach(option => {
        option.addEventListener('click', function() {
            // Убираем выделение со всех вариантов
            document.querySelectorAll('.color-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Добавляем выделение выбранному варианту
            this.classList.add('selected');
            
            // Можно добавить логику изменения основного изображения
            // в зависимости от выбранного цвета
            console.log('Выбран цвет:', this.getAttribute('title'));
        });
    });
</script>";
    $text2 .= '
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

echo $text2;

?>