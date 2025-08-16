<?php
// config.php - файл с настройками подключения к БД
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'rollershutters_db');
define('ADMIN_EMAIL', 'admin@example.com');
define('SITE_NAME', 'RollerShutters');
?>

<?php
// db_connect.php - подключение к базе данных
function db_connect() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

function create_tables() {
    $conn = db_connect();
    
    $sql = "CREATE TABLE IF NOT EXISTS contacts (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_processed TINYINT(1) DEFAULT 0
    )";
    
    $sql2 = "CREATE TABLE IF NOT EXISTS quotes (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        contact_id INT(6) UNSIGNED,
        width DECIMAL(10,2),
        height DECIMAL(10,2),
        material ENUM('aluminum', 'steel'),
        color VARCHAR(50),
        automation TINYINT(1) DEFAULT 0,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (contact_id) REFERENCES contacts(id)
    )";
    
    if ($conn->multi_query($sql . ";" . $sql2) === TRUE) {
        // Таблицы созданы
    } else {
        error_log("Error creating tables: " . $conn->error);
    }
    
    $conn->close();
}

// Вызываем создание таблиц при первом подключении
create_tables();
?>

<?php
// process_form.php - обработка формы
require_once 'config.php';
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Получаем и очищаем данные
$name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
$email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
$phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
$message = trim(filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING));

// Валидация
$errors = [];

if (empty($name)) {
    $errors['name'] = 'Please enter your name';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email';
}

if (empty($phone)) {
    $errors['phone'] = 'Please enter your phone number';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Сохраняем в базу данных
$conn = db_connect();

$stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $phone, $message);

if ($stmt->execute()) {
    $contact_id = $stmt->insert_id;
    
    // Отправляем email
    $to = ADMIN_EMAIL;
    $subject = "New contact request from " . SITE_NAME;
    $email_message = "
    <html>
    <head>
        <title>New Contact Request</title>
    </head>
    <body>
        <h2>New Contact Request</h2>
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Phone:</strong> {$phone}</p>
        <p><strong>Message:</strong> {$message}</p>
        <p>Received at: " . date('Y-m-d H:i:s') . "</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . SITE_NAME . " <noreply@example.com>\r\n";
    $headers .= "Reply-To: {$email}\r\n";
    
    mail($to, $subject, $email_message, $headers);
    
    // Ответ для AJAX
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will contact you shortly.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error saving your request. Please try again later.'
    ]);
}

$stmt->close();
$conn->close();
?>

<?php
// admin.php - простой админ-интерфейс для просмотра заявок
require_once 'config.php';
require_once 'db_connect.php';

session_start();

// Простая аутентификация
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_POST['username'] === 'admin' && $_POST['password'] === 'securepassword123') {
        $_SESSION['admin_logged_in'] = true;
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $error = "Invalid credentials";
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login</title>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; }
                .login-form { max-width: 400px; margin: 50px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
                .form-group { margin-bottom: 15px; }
                label { display: block; margin-bottom: 5px; }
                input { width: 100%; padding: 8px; box-sizing: border-box; }
                button { background: #3498db; color: white; border: none; padding: 10px 15px; cursor: pointer; }
                .error { color: red; }
            </style>
        </head>
        <body>
            <div class="login-form">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

$conn = db_connect();

// Обработка действий
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'process' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $conn->query("UPDATE contacts SET is_processed = 1 WHERE id = $id");
    }
}

// Получаем заявки
$contacts = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .processed { background-color: #e6ffe6; }
        .process-btn { background: #4CAF50; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .logout { float: right; }
    </style>
</head>
<body>
    <h1>Contact Requests <a href="?logout" class="logout">Logout</a></h1>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $contacts->fetch_assoc()): ?>
            <tr class="<?= $row['is_processed'] ? 'processed' : '' ?>">
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['message']) ?></td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                    <?php if (!$row['is_processed']): ?>
                        <a href="?action=process&id=<?= $row['id'] ?>" class="process-btn">Mark as Processed</a>
                    <?php else: ?>
                        Processed
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
?>