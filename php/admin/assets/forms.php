<?php 
include "../../system/db.php";
$table_name = "contacts";
if (isset($_GET["edit_id"])){
    $responce = pgQuery("SELECT * FROM ".$table_name." WHERE id = ".$_GET["edit_id"].";");
} else {
    $responce = pgQuery("SELECT * FROM ".$table_name." LIMIT 0;");
}   

var_dump($responce);
?>