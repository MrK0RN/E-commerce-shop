<?php
include("../system/db.php");

$g = pgQuery("SELECT * FROM contacts WHERE show_field = 'True';");

$cont = [
	"address" => "fa-map-marker-alt",
	"phone" => "fa-phone-alt",
	"mail" => "fa-envelope"
];

foreach ($g as $sc) {
	if (isset($cont[$sc["contact_name"]])){
		echo "<li><i class='fas ".$cont[$sc["contact_name"]]."'></i> ".$sc["contact_value"]."</li>";
	}
}
?>