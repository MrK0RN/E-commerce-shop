<?php
$currentFile = basename($_SERVER['PHP_SELF']);

$text2 = "<div class='sidebar'>
			<div class='sidebar-header'>
				<h2>Админ Панель</h2>
			</div>
			<div class='sidebar-menu'>";

$pages = [
	"Главная" => "index.php", 
	"Товары" => "goods.php", 
	"Галерея товаров" => "products.php",
	"Заказы" => "orders.php",
	"Контакты" => "contacts.php",
	"Админы" => "admins.php"
];
		
	  
foreach ($pages as $page => $address) {
	if ($currentFile == $address){
		$text2 .= "<a href='".$address."' class='active menu-item'>
			 ".$page."
	</a>";
	} else {
		$text2 .= "<a href='".$address."' class='menu-item'>
			 ".$page."
	</a>";
	}
}		  

$text2 .= "</div></div>";

echo $text2;

?>


<!--
<div class="sidebar">
	<div class="sidebar-header">
		<h2>Админ Панель</h2>
	</div>
	<div class="sidebar-menu">
		<a href="admin-dashboard.html" class="menu-item active">
			<i class="fas fa-tachometer-alt"></i> Главная
		</a>
		<a href="users.html" class="menu-item">
			<i class="fas fa-users"></i> Пользователи
		</a>
		<a href="products.html" class="menu-item">
			<i class="fas fa-boxes"></i> Товары
		</a>
		<a href="orders.html" class="menu-item">
			<i class="fas fa-shopping-cart"></i> Заказы
		</a>
		<a href="settings.html" class="menu-item">
			<i class="fas fa-cog"></i> Настройки
		</a>
		<a href="login.html" class="menu-item">
			<i class="fas fa-sign-out-alt"></i> Выход
		</a>
	</div>
</div>
-->


