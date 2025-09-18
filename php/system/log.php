<?php 

function appendToFile($filename, $content) {
    // Открываем файл для добавления содержимого (режим 'a')
    $file = fopen($filename, 'a');
    
    // Записываем содержимое
    fwrite($file, $content . PHP_EOL); // PHP_EOL добавляет перенос строки
    
    // Закрываем файл
    fclose($file);
}

function logger($author, $action, $log_text){
    $text = date(DATE_RFC2822)."|".$author.":".$action."->".$log_text;
    include "dir.php";
    #echo $text."<br>";
    appendToFile($logs, $text);
}

function log_db($log_text){
    include "dir.php";
    #echo $text."<br>";
    appendToFile($dir, $log_text);
}

?>