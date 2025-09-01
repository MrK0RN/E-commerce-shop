<?php
include "../../system/db.php";

// Проверяем, получены ли данные методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

// Получаем данные из формы
$name = $_POST['name'] ?? '';
$src_img = $_POST['src_img'] ?? '';
$price = $_POST['price'] ?? '';
$params = isset($_POST['params']) ? json_decode($_POST['params'], true) : [];
$options = isset($_POST['options']) ? json_decode($_POST['options'], true) : [];

// Проверяем обязательные поля
if (empty($name) || empty($src_img) || empty($price)) {
    echo json_encode(['success' => false, 'error' => 'Заполните все обязательные поля']);
    exit;
}

// Проверяем, редактируем ли существующий товар
$is_edit = false;
$good_id = null;

if (isset($_GET['good_id']) && is_numeric($_GET['good_id'])) {
    $is_edit = true;
    $good_id = $_GET['good_id'];

    // Проверяем существование товара
    $existing_good = pgQuery("SELECT * FROM goods WHERE id = $good_id;");
    if (empty($existing_good)) {
        echo json_encode(['success' => false, 'error' => 'Товар не найден']);
        exit;
    }
}

try {
    if ($is_edit) {
        // Редактирование существующего товара
        pgQuery("UPDATE goods SET name = '$name', src_img = '$src_img', price = '$price' WHERE id = $good_id;");

        // Удаляем старые характеристики и опции
        pgQuery("DELETE FROM params WHERE good_id = $good_id;");
        pgQuery("DELETE FROM options WHERE good_id = $good_id;");
    } else {
        // Создание нового товара
        $result = pgQuery("INSERT INTO goods (name, src_img, price) VALUES ('$name', '$src_img', '$price') RETURNING id;, false, true");
        $good_id = $result[0]['id'];
    }

    // Добавляем характеристики
    foreach ($params as $param) {
        $param_text = trim($param);
        if (!empty($param_text)) {
            pgQuery("INSERT INTO params (good_id, text) VALUES ($good_id, '$param_text');");
        }
    }

    // Добавляем опции
    foreach ($options as $option) {
        $option_text = trim($option);
        if (!empty($option_text)) {
            pgQuery("INSERT INTO options (good_id, text) VALUES ($good_id, '$option_text');");
        }
    }

    echo json_encode(['success' => true, 'good_id' => $good_id]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>