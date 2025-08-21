<?php
include "../../system/db.php";

if (!isset($_GET["id"]) || !isset($_GET["table_name"])) {
    echo "<div class='error'>Не переданы параметры id или table_name!</div>";
    exit;
}

$id = addslashes($_GET["id"]);
$table = addslashes($_GET["table_name"]);

$sql = "DELETE FROM $table WHERE id = '$id'";
$result = pgQuery($sql);

if ($result !== false) {
    echo "<div class='success'>Запись успешно удалена!</div>";
} else {
    echo "<div class='error'>Ошибка при удалении записи!</div>";
}
?>
