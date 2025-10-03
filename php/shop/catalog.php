<?php
include "../system/db.php";
$query = "SELECT * FROM categories ORDER BY created_at DESC";
$result = pgQuery($query);
$files = [];
$ids = [];
foreach ($result as $id){
    $dir = "../data/categories/".$id['id']."/";
    if (is_dir($dir)) {
        $scan = @scandir($dir);
        if (is_array($scan)) {
            $ids[$id['id']] = $id["name"];
            $files[$id['id']] = $dir . array_values(array_filter($scan, fn($f) => $f !== '.' && $f !== '..' && !is_dir("$dir/$f")))[0];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профессиональная карточка товара</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0;}
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!--<link rel="stylesheet" href="../assets/css/styles.css?v=1">-->
    <link rel="stylesheet" href="../assets/css/catalog_ind.css?v=1">
    <style>
        .category-header {
            max-width: 1200px;
            margin: 0 auto 30px;
            padding: 0 40px;
            display: flex;
            justify-content: left;
            border-bottom: 1px solid #e0e0e0;
        }
    </style>
</head>
<body style="padding-top: 200px">
    <?php
    include "../assets/header_black.php";?>
    <section class="catalog-section" style="padding: 0;">
        <div class="category-header">
            <h1 style=" font-size: 32px;
                        font-weight: 700;
                        color: #222;
                        margin-bottom: 15px;">Catalog</h1>
        </div>
        <div class="container">
            <div class="catalog-grid">
            <?php foreach ($files as $key => $value): ?>
                <div class="catalog-card" onclick="window.location.href='category.php?id=<?=$key?>'">
                <div class="card-image">
                <img src="<?= $value ?>" alt="Название категории 1">
                <div class="image-overlay"></div>
                <div class="card-caption">
                    <h3 class="category-title"><?= htmlspecialchars($ids[$key]) ?></h3>
                </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
	include "../assets/brands.php";
	include "../assets/footer.php";
	?>