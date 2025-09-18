<?php
include("system/db.php");

$g = pgQuery("SELECT * FROM contacts WHERE show_field = 'True';");

$cont = [
	"address" => "fa-map-marker-alt",
	"phone" => "fa-phone-alt",
	"mail" => "fa-envelope",
	"work_hours" => "fa-clock"
];

foreach ($g as $cu) {

    //echo $cu["data_name"];
    if (isset($cont[$cu["contact_name"]])){
        echo "<div>
            <i class='fas ".$cont[$cu["contact_name"]]."'></i>
            <span>".$cu["contact_value"]."</span>
        </div>";
    }

    
    
	// echo "<li><i class='fas ".$cont[$cu["data_name"]]."'></i> ".$cu["data_value"]."</li>";
}
?>