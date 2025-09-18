<?php

$baseDir = __DIR__ . '/../../';
$dbFile = $baseDir . 'system/db.php';

include $dbFile;

$g = pgQuery("SELECT * FROM social_networks WHERE show_field = 'True';");

$socials = [
    "meta" => "fa-facebook-f",       // Meta (Facebook) использует иконку Facebook
    "x" => "fa-x-twitter",           // Правильное название для X (бывший Twitter)
    "instagram" => "fa-instagram",   // Верно
    "linkedin" => "fa-linkedin-in"   // Верно
];

foreach ($g as $sc) {
	if (isset($socials[$sc["social_network"]])){
		echo "<a href='".$sc["link"]."'><i class='fab ".$socials[$sc["social_network"]]."'></i></a>";
	}
}
?>
