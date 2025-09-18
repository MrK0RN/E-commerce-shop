<?php
include("system/db.php");

$g = pgQuery("SELECT * FROM navHeader WHERE show_field = 'True';");

?>

<nav class="nav-header_8as4bd transparent" id="navHeader">
    <div class="nav-container_8as4bd">
        <ul class="nav-menu_8as4bd">
            <?php
                foreach ($g as $nav){
                    echo '<li class="nav-item_8as4bd"><a href="'.$nav["page_link"].'" class="nav-link_8as4bd">'.$nav["page"].'</a></li>';
                }
            ?>
            <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">О компании</a></li>
            <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Услуги</a></li>
            <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Портфолио</a></li>
            <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Цены</a></li>
            <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Контакты</a></li>
        </ul>
    </div>
</nav>
    <!-- Мобильное меню -->
<div class="mobile-menu_8as4bd" id="mobileMenu">
    <ul>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Главная</a></li>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">О компании</a></li>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Услуги</a></li>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Портфолио</a></li>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Цены</a></li>
        <li class="nav-item_8as4bd"><a href="#" class="nav-link_8as4bd">Контакты</a></li>
    </ul>
</div>