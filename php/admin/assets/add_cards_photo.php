<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die('Укажите ID товара: ?id=123');
}
$dir = __DIR__ . "/../../data/cards/$id";
if (!is_dir($dir)) @mkdir($dir, 0777, true);
$files = [];
if (is_dir($dir)) {
    $scan = @scandir($dir);
    if (is_array($scan)) {
        $files = array_values(array_filter($scan, fn($f) => $f !== '.' && $f !== '..' && !is_dir("$dir/$f")));  
    }
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Фото товара <?= $id ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background: #f7f7fa; color: #222; }
        h2 { margin: 0 0 16px; font-weight: 600; }

        .topbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .btn { display: inline-block; padding: 8px 14px; border-radius: 8px; border: 1px solid transparent; text-decoration: none; cursor: pointer; font-size: 14px; transition: .15s ease-in-out; }
        .btn-secondary { background: #fff; border-color: #d0d5dd; color: #344054; }
        .btn-secondary:hover { background: #f9fafb; }
        .btn-primary { background: #3b82f6; border-color: #3b82f6; color: #fff; }
        .btn-primary:hover { background: #2563eb; border-color: #2563eb; }

        .photos { display: flex; flex-wrap: wrap; gap: 12px; }
        .photo { position: relative; width: 150px; height: 150px; border-radius: 10px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .photo img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .photo:hover { box-shadow: 0 4px 12px rgba(0,0,0,.12); }
        .del { position: absolute; top: 6px; right: 6px; background: #ef4444; color: #fff; border: none; cursor: pointer; width: 28px; height: 28px; border-radius: 6px; line-height: 28px; text-align: center; opacity: .9; }
        .del:hover { opacity: 1; }

        #drop { border: 2px dashed #cbd5e1; padding: 24px; margin-top: 20px; text-align: center; background: #fff; border-radius: 10px; }
        #drop:hover { border-color: #94a3b8; }
        #drop input[type="file"] { margin-bottom: 10px; }
        #drop button { margin-top: 8px; }
    </style>
</head>
<body>

<div class="topbar">
    <a href="../products.php" class="btn btn-secondary">← Назад к товарам</a>
    <h2>Фото товара <?= htmlspecialchars($id) ?></h2>
    <span></span>
    <!-- spacer for alignment -->
</div>

<div class="photos">
    <?php foreach ($files as $f): ?>
        <div class="photo" data-name="<?= htmlspecialchars($f) ?>">
            <img src="../../data/cards/<?= $id ?>/<?= rawurlencode($f) ?>">
            <button class="del">×</button>
        </div>
    <?php endforeach; ?>
</div>

<form id="drop" enctype="multipart/form-data">
    <input type="file" name="files[]" multiple accept="image/*">
    <button type="submit">Загрузить</button>
</form>

<script>
const id = <?= $id ?>;

// удаление
document.addEventListener('click', e => {
    if (!e.target.classList.contains('del')) return;
    const name = e.target.parentElement.dataset.name;
    fetch('upload2.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({action: 'delete', id, name})
    }).then(() => location.reload());
});

// загрузка
document.getElementById('drop').addEventListener('submit', e => {
    e.preventDefault();
    const fd = new FormData(e.target);
    fd.append('id', id);
    fetch('upload2.php', {method: 'POST', body: fd})
        .then(() => location.reload());
});
</script>

</body>
</html>