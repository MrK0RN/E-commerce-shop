<?php 
include "../auth.php";
include "../../system/db.php";
$table_name = $_GET["table_name"];
function cleanAlphanumeric($string) {
    // Оставляем буквы, цифры, пробелы и запятые
    $string = preg_replace('/[^a-zA-Z0-9\s,]/', '', $string);
    return trim(preg_replace('/\s+/', ' ', $string));
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Исключить из POST-обработки
    $fields = array_filter(array_keys($_POST), function($k) {
        return $k !== 'id' && $k !== 'created_at' && $k !== 'updated_at';
    });
    $values = array_map(function($k) { return "'" . addslashes($_POST[$k]) . "'"; }, $fields);
    if (isset($_GET["edit_id"])) {
        // Обновление записи
        $set = [];
        foreach ($_POST as $key => $value) {
            if ($key === 'id' || $key === 'created_at' || $key === 'updated_at') continue;
            $set[] = "$key = '" . addslashes($value) . "'";
        }
        $set_str = implode(", ", $set);
        $sql = "UPDATE $table_name SET $set_str WHERE id = '" . addslashes($_GET["edit_id"]) . "'";
        $result = pgQuery($sql);
        if ($result !== false) {
            echo "<div class='success'>Запись успешно обновлена! Перенаправление...</div>";
            echo "<script>setTimeout(function(){ window.location.href = '/admin'; }, 1000);</script>";
        } else {
            echo "<div class='error'>Ошибка при обновлении записи!</div>";
            echo "<script>setTimeout(function(){ window.location.href = '/admin'; }, 1000);</script>";
        }
    } else {
        // Вставка новой записи
        $fields_str = implode(", ", $fields);
        $values_str = implode(", ", $values);
        $sql = "INSERT INTO $table_name ($fields_str) VALUES ($values_str)";
        $result = pgQuery($sql);
        if ($result !== false) {
            echo "<div class='success'>Запись успешно добавлена! Перенаправление...</div>";
            echo "<script>setTimeout(function(){ window.location.href = '/admin'; }, 3000);</script>";
        } else {
            echo "<div class='error'>Ошибка при добавлении записи!</div>";
            echo "<script>setTimeout(function(){ window.location.href = '/admin'; }, 3000);</script>";
        }
    }
} else {
    $text2 = "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Standard Form</title>
        <link rel='stylesheet' href='../css/forms.css'>
        <!-- You might also want to link to a global admin style if it exists -->
        <!-- <link rel='stylesheet' href='../css/styles.css'> -->
    </head>
    <body>
        <div class='form-container'>";
    
    $text2 .= "<h2>".$table_name."</h2><form action='#' method='POST'>";
    
    if (isset($_GET["edit_id"])){
        $responce = pgQuery("SELECT * FROM ".$table_name." WHERE id = ".$_GET["edit_id"].";")[0];
        foreach ($responce as $key => $value) {
            if ($key === 'id' || $key === 'created_at' || $key === 'updated_at') continue;
            $text2 .= "<div class='form-group'>";
            $text2 .= "<label for='".$key."'>".$key."</label>";
            $text2 .= "<input type='text' id='".$key."' name='".$key."' class='form-control' value='".$value."' required>";
            $text2 .= "</div>";
        }    
    } else {
        $responce = pgQuery("SELECT column_name FROM information_schema.columns WHERE table_name = '".$table_name."';");
        foreach ($responce as $row) {
            if ($row["column_name"] === 'id' || $row["column_name"] === 'created_at' || $row["column_name"] === 'updated_at') continue;
            $text2 .= "<div class='form-group'>";
            $text2 .= "<label for='".$row["column_name"]."'>".$row["column_name"]."</label>";
            $text2 .= "<input type='text' id='".$row["column_name"]."' name='".$row["column_name"]."' class='form-control' placeholder='Enter your ".$row["column_name"]."' required>";
            $text2 .= "</div>";
        }    
    }
    $text2 .= "<button type='submit' class='submit-btn'>Submit Form</button>
            <button type='button' class='submit-btn btn-cancel'>Cancel</button>
        </form></form></div></body></html>";
    echo $text2;
}


?>