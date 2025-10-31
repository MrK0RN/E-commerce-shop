<?php
include "../../system/db.php";

// Проверяем, получены ли данные методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Неверный метод запроса']);
    exit;
}

// Получаем данные из формы
$name = $_POST['name'] ?? '';
$score = $_POST['score'] ?? '';
$old_price = $_POST['old_price'] ?? '';
$price = $_POST['price'] ?? '';
$delivery = $_POST['delivery'] ?? '';
$description = $_POST['description'] ?? '';
$params = isset($_POST['params']) ? json_decode($_POST['params'], true) : [];
$options = isset($_POST['options']) ? json_decode($_POST['options'], true) : [];
$sizes = isset($_POST['sizes']) ? json_decode($_POST['sizes'], true) : [];

// Проверяем обязательные поля
if (empty($name) || empty($price) || empty($old_price) || empty($delivery) || empty($description)) {
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
        pgQuery("UPDATE goods SET name = '$name', score = '$score', old_price = '$old_price', price = '$price', delivery = '$delivery', description = '$description' WHERE id = $good_id;");

        // Удаляем старые характеристики, опции и размеры
        pgQuery("DELETE FROM params WHERE good_id = $good_id;");
        pgQuery("DELETE FROM options WHERE good_id = $good_id;");
        pgQuery("DELETE FROM sizes WHERE good_id = $good_id;");
    } else {
        // Создание нового товара
        $result = pgQuery("INSERT INTO goods (name, score, old_price, price, delivery, description) VALUES ('$name', '$score', '$old_price', '$price', '$delivery', '$description') RETURNING id", false, true);
        $good_id = $result[0]['id'];
    }

    // Добавляем размеры
    foreach ($sizes as $size) {
        $size_name = trim($size['size_name']);
        $price_addition = floatval($size['price_addition']);
        
        if (!empty($size_name) && $price_addition >= 0) {
            pgQuery("INSERT INTO sizes (good_id, size_name, price_addition) VALUES ($good_id, '$size_name', $price_addition);");
        }
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
    $dir = "../../shop/images/".$good_id;          // имя папки
    if (!is_dir($dir) && !$is_edit) {       // проверяем, нет ли её уже
        mkdir($dir, 0777, true); // true = создать всю цепочку вложенных каталогов
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}