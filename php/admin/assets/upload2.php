<?php
header('Content-Type: application/json');
// сначала пробуем обработать JSON-запрос на удаление,
// т.к. для него id передается в теле JSON, а не в $_POST
$raw = file_get_contents('php://input');
$json = $raw ? json_decode($raw, true) : null;
if (is_array($json) && ($json['action'] ?? null) === 'delete') {
    $id = intval($json['id'] ?? 0);
    if (!$id) {
        echo json_encode(['error' => 'no id']);
        exit;
    }
    $dir = __DIR__ . "/../../shop/cards/$id";
    if (!is_dir($dir)) @mkdir($dir, 0777, true);
    $file = basename($json['name'] ?? '');
    if ($file !== '') {
        @unlink("$dir/$file");
    }
    echo json_encode(['ok' => true]);
    exit;
}

// обычные запросы (загрузка файлов) принимают id из $_POST/$_GET
$id = intval($_POST['id'] ?? $_GET['id'] ?? 0);
if (!$id) die(json_encode(['error' => 'no id']));

$dir = __DIR__ . "/../../shop/cards/$id";
if (!is_dir($dir)) @mkdir($dir, 0777, true);

// загрузка
if (!empty($_FILES['files']['name'][0])) {
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    foreach ($_FILES['files']['tmp_name'] as $i => $tmp) {
        if (!$tmp) continue;
        $ext = strtolower(pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) continue;
        $safeExt = $ext ?: 'jpg';
        $name = uniqid('img_', true) . '.' . $safeExt;
        @move_uploaded_file($tmp, "$dir/$name");
    }
}
echo json_encode(['ok' => true]);