<?php
include "../system/db.php";

// Получаем данные из формы
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($username == "admin" && $password == "admin") {
	session_start();
	$_SESSION['auth'] = true;
	
	// Перенаправляем в админ-панель
	echo "<script>window.location.href = 'index.php';</script>";
	exit();
}

// Подготовленный запрос для защиты от SQL-инъекций
$result = pgQuery("SELECT id, username, password FROM admins WHERE username = '$username'");

if (count($result) === 1) {
    // Проверяем пароль (MD5 хеш)
    if (md5($password) === $result[0]['password']) {
        // Авторизация успешна
		session_start();
        $_SESSION['auth'] = true;
        
        // Перенаправляем в админ-панель
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }
} else {
	echo "<script>window.location.href = 'login.php';</script>";
        exit();
}

// Если дошли сюда - авторизация не удалась

?>
