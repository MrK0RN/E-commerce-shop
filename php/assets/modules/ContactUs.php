<?php
include("../system/db.php");

$g = pgQuery("SELECT * FROM contacts WHERE block = 'footer' AND sect = 'cu' AND show = True;");

$cont = [
	"address" => "fa-map-marker-alt",
	"phone" => "fa-phone-alt",
	"mail" => "fa-envelope",
	"working_hours" => "fa-clock"
];

foreach ($g as $cu) {

    //echo $cu["data_name"];

    echo "<div>
        <i class='fas ".$cont[$cu["data_name"]]."'></i>
        <span>".$cu["data_value"]."</span>
    </div>";
    
	// echo "<li><i class='fas ".$cont[$cu["data_name"]]."'></i> ".$cu["data_value"]."</li>";
}
?>