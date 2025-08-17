<?php
include "auth.php";
include "../system/db.php";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная - Админ панель</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #34495e;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --info: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f6fa;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Сайдбар */
        .sidebar {
            width: 250px;
            background: var(--primary);
            color: white;
        }
        
        .sidebar-header {
            padding: 20px;
            background: var(--secondary);
            text-align: center;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            color: var(--light);
            text-decoration: none;
            display: block;
            transition: all 0.3s;
        }
        
        .menu-item:hover, .menu-item.active {
            background: var(--secondary);
            border-left: 4px solid var(--accent);
        }
        
        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Основное содержимое */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        /* Карточки статистики */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stat-card i {
            font-size: 30px;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .stat-card.users i { color: var(--info); }
        .stat-card.products i { color: var(--success); }
        .stat-card.orders i { color: var(--warning); }
        .stat-card.revenue i { color: var(--accent); }
        
        .stat-card h3 {
            margin: 0;
            font-size: 14px;
            color: #777;
            text-transform: uppercase;
        }
        
        .stat-card .value {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        /* Последние действия */
        .recent-activity {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .activity-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--primary);
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-time {
            font-size: 12px;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Сайдбар -->
		<?php
		include "assets/leftMenu.php";
		?>
        
        <!-- Основное содержимое -->
        <div class="main-content">
            <div class="header">
                <h1>Главная панель</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=2c3e50&color=fff" alt="Admin">
                    <div>
                        <div>Администратор</div>
                        <!--<small>Последний вход: 15.05.2023 14:30</small>-->
                    </div>
                </div>
            </div>
            
            <!-- Карточки статистики -->
            <div class="stats-container">
                <div class="stat-card users">
                    <i class="fas fa-users"></i>
                    <h3>Пользователи</h3>
                    <div class="value">1 248</div>
                    <!--<small>+5% за месяц</small>-->
                </div>
                
                <div class="stat-card products">
                    <i class="fas fa-boxes"></i>
                    <h3>Товары</h3>
                    <div class="value">576</div>
                    <!--<small>+12 новых</small>-->
                </div>
                
                <div class="stat-card orders">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Новые заказы</h3>
                    <div class="value">23</div>
                    <!--<small>Требуют обработки</small>-->
                </div>
                <!--
                <div class="stat-card revenue">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>Доход</h3>
                    <div class="value">124 560 ₽</div>
                    <small>За текущий месяц</small>
                </div>-->
            </div>
            
            <!-- Последние действия
            <div class="recent-activity">
                <h2><i class="fas fa-history"></i> Последние действия</h2>
                
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <strong>Новый пользователь</strong>
                        <p>Зарегистрирован пользователь example@mail.com</p>
                        <div class="activity-time">10 минут назад</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="activity-content">
                        <strong>Новый заказ</strong>
                        <p>Оформлен заказ #1254 на сумму 5 600 ₽</p>
                        <div class="activity-time">35 минут назад</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="activity-content">
                        <strong>Добавлен товар</strong>
                        <p>Добавлен новый товар "Смартфон XYZ" в категорию "Электроника"</p>
                        <div class="activity-time">2 часа назад</div>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <div class="activity-content">
                        <strong>Обновление системы</strong>
                        <p>Установлено обновление админ-панели версии 2.1.3</p>
                        <div class="activity-time">Вчера, 18:45</div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</body>
</html>
