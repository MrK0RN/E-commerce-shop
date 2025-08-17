<?php

$baseDir = __DIR__ . '/../../';
$dbFile = $baseDir . 'system/db.php';

include $dbFile;

$g = pgQuery("SELECT * FROM contacts WHERE block = 'footer' AND sect = 'sc' AND show_field = True;");

$socials = [
	"facebook" => "fa-facebook-f",
	"x" => "fa-x",
	"instagram" => "fa-instagram",
	"linkedin" => "fa-linkedin-in"
];

foreach ($g as $sc) {
	echo "<a href='".$sc["data_value"]."'><i class='fab ".$socials[$sc["data_name"]]."'></i></a>";
}
?>
