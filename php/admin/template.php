<?php
include "auth.php";
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
    echo "<title>".$title." - Админ панель</title>";
	?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Сайдбар -->
		<?php
		include "assets/leftMenu.php";
		?>
        
        <!-- Основное содержимое -->
        <div class="main-content">
			<?php
			include "assets/topMenu.php";
			?>
			<?php
			echo $main;
			?>
		</div>
	</div>
</body>
