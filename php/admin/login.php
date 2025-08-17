<?php

session_start();
if ($_SESSION["auth"] == True){
	header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админ-панель</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a2e;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #fff;
        }
        .login-box {
            background: #16213e;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            width: 350px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 25px;
            color: #e94560;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            background: #0f3460;
            color: #fff;
            font-size: 16px;
        }
        .input-group input:focus {
            outline: 2px solid #e94560;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #e94560;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .login-btn:hover {
            background: #d23352;
        }
        .error-message {
            color: #ff6b6b;
            margin-top: 15px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Вход в админ-панель</h2>
        <form action="login-process.php" method="POST">
            <div class="input-group">
                <label for="username">Логин:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Войти</button>
        </form>
        <div class="error-message" id="errorMessage">
            Неверный логин или пароль
        </div>
    </div>

    <script>
        // Показываем сообщение об ошибке, если оно есть в URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('error')) {
            document.getElementById('errorMessage').style.display = 'block';
        }
    </script>
</body>
</html>
