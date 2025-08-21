<?php
include("../system/db.php");

$g = pgQuery("SELECT * FROM contacts WHERE block = 'footer' AND sect = 'rc' AND show = True;");

$cont = [
	"address" => "fa-map-marker-alt",
	"phone" => "fa-phone-alt",
	"mail" => "fa-envelope"
]

foreach ($g as $sc) {
	echo "<li><i class='fas ".$cont[$sc["data_name"]]."'></i> ".$sc["data_value"]."</li>";
?>