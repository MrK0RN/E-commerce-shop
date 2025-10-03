<?php
// config.php - Настройки базы данных
include "../system/db.php";
define('UPLOAD_DIR', '../data/projects/images');
// Создание таблицы если не существует
function createTable() {
    $query = "CREATE TABLE IF NOT EXISTS photos (
        id SERIAL PRIMARY KEY,
        filename VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    pgQuery($query);
}

// Функция для загрузки фотографии
function uploadPhoto($file, $description) {
    // Проверка директории для загрузок
    if (!file_exists(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Валидация файла
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Ошибка загрузки файла'];
    }
    
    // Проверка типа файла
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($file['tmp_name']);
    
    if (!in_array($file_type, $allowed_types)) {
        return ['success' => false, 'message' => 'Разрешены только изображения JPEG, PNG, GIF и WebP'];
    }
    
    // Генерация уникального имени файла
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $file_extension;
    $filepath = UPLOAD_DIR . $filename;
    
    // Перемещение файла
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Ошибка сохранения файла'];
    }
    
    // Сохранение в базу данных
    $query = "INSERT INTO photos (filename, description) VALUES ('".$filename."', '".$description."') RETURNING id";
    $result = pgQuery($query, false, true);
    
    if ($result) {
        return ['success' => true, 'message' => 'Фотография успешно загружена', 'filename' => $filename];
    } else {
        // Удаляем файл если запись в БД не удалась
        unlink($filepath);
        return ['success' => false, 'message' => 'Ошибка сохранения в базу данных'];
    }
}

// Функция для получения всех фотографий
function getAllPhotos() {
    $query = "SELECT * FROM photos ORDER BY created_at DESC";
    $result = pgQuery($query);
    
    $photos = [];
    foreach ($result as $row) {
        $photos[] = $row;
    }
    
    return $photos;
}

// Функция для удаления фотографии
function deletePhoto($id) {
    // Сначала получаем информацию о файле
    $query = "SELECT filename FROM photos WHERE id = ".$id;
    $photo = pgQuery($query);
    
    if ($photo) {
        // Удаляем файл
        $filepath = UPLOAD_DIR . $photo['filename'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // Удаляем запись из базы данных
        $query = "DELETE FROM photos WHERE id = ".$id;
        pgQuery($query);
        
        return ['success' => true, 'message' => 'Фотография удалена'];
    }
    
    return ['success' => false, 'message' => 'Фотография не найдена'];
}

// Основная логика приложения
createTable();

// Обработка POST запросов
$message = '';
$message_class = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'upload':
                if (isset($_FILES['photo']) && isset($_POST['description'])) {
                    $result = uploadPhoto($_FILES['photo'], $_POST['description']);
                    $message = $result['message'];
                    $message_class = $result['success'] ? 'success' : 'error';
                }
                break;
                
            case 'delete':
                if (isset($_POST['id'])) {
                    $result = deletePhoto($_POST['id']);
                    $message = $result['message'];
                    $message_class = $result['success'] ? 'success' : 'error';
                }
                break;
        }
    }
}

// Получаем все фотографии для отображения
$photos = getAllPhotos();

// Формируем HTML код в переменной $main
$main = '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Галерея фотографий</title>
    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .upload-form { background: #f5f5f5; padding: 20px; margin-bottom: 30px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        textarea { height: 80px; resize: vertical; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #005a87; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .photo-card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; }
        .photo-card img { max-width: 100%; height: auto; display: block; margin-bottom: 10px; }
        .photo-description { margin: 10px 0; color: #666; }
        .photo-date { font-size: 12px; color: #999; }
        .delete-btn { background: #dc3545; padding: 5px 10px; font-size: 12px; }
        .delete-btn:hover { background: #c82333; }
        .message { padding: 10px; margin: 10px 0; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Галерея фотографий</h1>
';

// Добавляем сообщение если есть
if (!empty($message)) {
    $main .= '
        <div class="message ' . $message_class . '">
            ' . htmlspecialchars($message) . '
        </div>
    ';
}

// Добавляем форму загрузки
$main .= '
        <div class="upload-form">
            <h2>Добавить новую фотографию</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload">
                
                <div class="form-group">
                    <label for="photo">Фотография:</label>
                    <input type="file" name="photo" id="photo" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание:</label>
                    <textarea name="description" id="description" placeholder="Введите описание фотографии..." required></textarea>
                </div>
                
                <button type="submit">Загрузить фотографию</button>
            </form>
        </div>
        
        <div class="gallery">
';

// Добавляем галерею фотографий
if (empty($photos)) {
    $main .= '<p>Пока нет загруженных фотографий.</p>';
} else {
    foreach ($photos as $photo) {
        $main .= '
            <div class="photo-card">
                <img src="' . UPLOAD_DIR . htmlspecialchars($photo['filename']) . '" 
                     alt="' . htmlspecialchars($photo['description']) . '">
                <div class="photo-description">
                    ' . htmlspecialchars($photo['description']) . '
                </div>
                <div class="photo-date">
                    Загружено: ' . date('d.m.Y H:i', strtotime($photo['created_at'])) . '
                </div>
                <form method="POST" style="margin-top: 10px;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="' . $photo['id'] . '">
                    <button type="submit" class="delete-btn" onclick="return confirm(\'Удалить эту фотографию?\')">
                        Удалить
                    </button>
                </form>
            </div>
        ';
    }
}

// Завершаем HTML
$main .= '
        </div>
    </div>
</body>
</html>
';

// Выводим содержимое переменной $main
?>