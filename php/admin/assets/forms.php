<?php 
include "../../system/db.php";
$table_name = $_GET["table_name"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
} else {
    $text2 = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Standard Form</title>
        <link rel='stylesheet' href='forms.css'>
        <!-- You might also want to link to a global admin style if it exists -->
        <!-- <link rel='stylesheet' href='../css/styles.css'> -->
    </head>
    <body>
        <div class='form-container'>";
    
    $text2 .= "<h2>".$table_name."</h2><form action='#' method='POST'>";
    
    if (isset($_GET["edit_id"])){
        $responce = pgQuery("SELECT * FROM ".$table_name." WHERE id = ".$_GET["edit_id"].";")[0];
        foreach ($responce as $key => $value) {
            $text2 .= "<div class='form-group'>";
            $text2 .= "<label for= '".$key."'>".$key."</label>";
            $text2 .= "<input type='text' id='".$key."' name='".$key."' class='form-control' value='".$key."' required>";
            $text2 .= "</div>";
        }    
    } else {
        $responce = pgQuery("SELECT column_name FROM information_schema.columns WHERE table_name = '".$table_name."';");
        foreach ($responce as $row) {
            $text2 .= "<div class='form-group'>";
            $text2 .= "<label for= '".$row["column_name"]."'>".$row["column_name"]."</label>";
            $text2 .= "<input type='text' id='".$row["column_name"]."' name='".$row["column_name"]."' class='form-control' placeholder='Enter your ".$row["column_name"]."' required>";
            $text2 .= "</div>";
        }    
    }
    $text2 .= "</form></div></body></html>";
    echo $text2;
}


?>