<?php
include "../../system/db.php";
$red = 0;
if (isset($_GET["id"])){
    $red++;
    $goods = pgQuery("SELECT * FROM goods WHERE id = ".$_GET["id"].";");
    $params = pgQuery("SELECT * FROM params WHERE good_id = ".$_GET["id"].";");
    $options = pgQuery("SELECT * FROM options WHERE good_id = ".$_GET["id"].";");
    $sizes = pgQuery("SELECT * FROM sizes WHERE good_id = ".$_GET["id"].";");

    // Если товар найден, заполняем данные
    if (!empty($goods)) {
        $good = $goods[0];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание товаров</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .dynamic-items {
            margin-top: 15px;
        }

        .item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }

        .size-item {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .item input {
            flex-grow: 1;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }

        .btn-danger {
            background-color: #f44336;
            color: white;
        }

        .btn-secondary {
            background-color: #2196F3;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }

        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }

        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
    </style>
</head>
<body>
<div class="container">
    <?php
    if ($red && !empty($good)) {
        echo "<h1>Редактировать товар</h1>";
    } else {
        echo "<h1>Создание нового товара</h1>";
    }
    ?>
    <div id="message"></div>

    <form id="create-form">
        <?php if ($red && !empty($good)): ?>
            <input type="hidden" name="good_id" value="<?php echo $_GET['id']; ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label for="name">Id Категории:</label>
            <input type="text" id="category_id" value="<?php echo !empty($good['category_id']) ? $good['category_id'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="name">Название товара:</label>
            <input type="text" id="name" value="<?php echo !empty($good['name']) ? $good['name'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="name">Рейтинг товара: (дробное число, необязательно)</label>
            <input type="text" id="score" value="<?php echo !empty($good['score']) ? $good['score'] : ''; ?>">
        </div>

        <div class="form-group">
            <label for="price">Цена до скидки:</label>
            <input type="text" id="old_price" value="<?php echo !empty($good['old_price']) ? $good['old_price'] : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Цена:</label>
            <input type="text" id="price" value="<?php echo !empty($good['price']) ? $good['price'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="price">Описание доставки: (например: сегодня (курьером за 3 ч.))</label>
            <input type="text" id="delivery" value="<?php echo !empty($good['delivery']) ? $good['delivery'] : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Описание товара:</label>
            <textarea name="description" id="description"><?php echo !empty($good['description']) ? $good['description'] : ''; ?></textarea>
        </div>

        <!-- Секция размеров -->
        <div class="form-group">
            <label>Размеры и надбавки к цене:</label>
            <div class="dynamic-items" id="sizes-container">
                <?php
                if ($red && !empty($sizes)) {
                    foreach ($sizes as $size) {
                        echo '<div class="size-item">
                                <input type="text" name="size_names[]" placeholder="Название размера" value="' . htmlspecialchars($size["size_name"]) . '">
                                <input type="number" name="price_additions[]" placeholder="Надбавка к цене" value="' . htmlspecialchars($size["price_addition"]) . '" step="0.01">
                                <button type="button" class="btn btn-danger" onclick="removeSize(this)">Удалить</button>
                              </div>';
                    }
                } else {
                    echo '<div class="size-item">
                            <input type="text" name="size_names[]" placeholder="Название размера">
                            <input type="number" name="price_additions[]" placeholder="Надбавка к цене" step="0.01">
                            <button type="button" class="btn btn-danger" onclick="removeSize(this)">Удалить</button>
                          </div>';
                }
                ?>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addSize()">Добавить размер</button>
        </div>

        <div class="form-group">
            <label>Характеристики:</label>
            <div class="dynamic-items" id="params-container">
                <?php
                if ($red && !empty($params)) {
                    foreach ($params as $param) {
                        echo '<div class="item">
                                    <input type="text" name="params[]" placeholder="Характеристика" value="' . htmlspecialchars($param["text"]) . '">
                                    <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
                                </div>';
                    }
                } else {
                    echo '<div class="item">
                            <input type="text" name="params[]" placeholder="Характеристика">
                            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
                          </div>';
                }
                ?>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addParam()">Добавить характеристику</button>
        </div>

        <div class="form-group">
            <label>Опции:</label>
            <div class="dynamic-items" id="options-container">
                <?php
                if ($red && !empty($options)) {
                    foreach ($options as $option) {
                        echo '<div class="item">
                                    <input type="text" name="options[]" placeholder="Опция" value="' . htmlspecialchars($option["text"]) . '">
                                    <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
                                </div>';
                    }
                } else {
                    echo '<div class="item">
                            <input type="text" name="options[]" placeholder="Опция">
                            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
                          </div>';
                }
                ?>
            </div>
            <button type="button" class="btn btn-secondary" onclick="addOption()">Добавить опцию</button>
        </div>

        <?php
        if ($red && !empty($good)) {
            echo '<button type="submit" class="btn btn-primary">Редактировать товар</button>';
        } else {
            echo '<button type="submit" class="btn btn-primary">Создать товар</button>';
        }
        ?>
    </form>
</div>

<script>
    // Добавление характеристик
    function addParam() {
        const container = document.getElementById('params-container');
        const div = document.createElement('div');
        div.className = 'item';
        div.innerHTML = `
                <input type="text" name="params[]" placeholder="Характеристика">
                <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
            `;
        container.appendChild(div);
    }

    // Добавление опций
    function addOption() {
        const container = document.getElementById('options-container');
        const div = document.createElement('div');
        div.className = 'item';
        div.innerHTML = `
                <input type="text" name="options[]" placeholder="Опция">
                <button type="button" class="btn btn-danger" onclick="removeItem(this)">Удалить</button>
            `;
        container.appendChild(div);
    }

    // Добавление размеров
    function addSize() {
        const container = document.getElementById('sizes-container');
        const div = document.createElement('div');
        div.className = 'size-item';
        div.innerHTML = `
                <input type="text" name="size_names[]" placeholder="Название размера">
                <input type="number" name="price_additions[]" placeholder="Надбавка к цене" step="0.01">
                <button type="button" class="btn btn-danger" onclick="removeSize(this)">Удалить</button>
            `;
        container.appendChild(div);
    }

    // Удаление элемента характеристик/опций
    function removeItem(button) {
        // Не позволяем удалить последнее поле
        const container = button.closest('.dynamic-items');
        if (container.querySelectorAll('.item').length > 1) {
            button.parentElement.remove();
        }
    }

    // Удаление элемента размеров
    function removeSize(button) {
        const container = document.getElementById('sizes-container');
        if (container.querySelectorAll('.size-item').length > 1) {
            button.parentElement.remove();
        }
    }

    // Отправка формы
    document.getElementById('create-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        <?php if ($red && !empty($good)): ?>
        formData.append('good_id', '<?php echo $_GET["id"]; ?>');
        <?php endif; ?>
        
        formData.append('category_id', document.getElementById('category_id').value);
        formData.append('name', document.getElementById('name').value);
        formData.append('score', document.getElementById('score').value);
        formData.append('old_price', document.getElementById('old_price').value);
        formData.append('price', document.getElementById('price').value);
        formData.append('delivery', document.getElementById('delivery').value);
        formData.append('description', document.getElementById('description').value);

        // Собираем данные о размерах
        const sizes = [];
        const sizeNames = document.querySelectorAll('input[name="size_names[]"]');
        const priceAdditions = document.querySelectorAll('input[name="price_additions[]"]');
        
        for (let i = 0; i < sizeNames.length; i++) {
            const sizeName = sizeNames[i].value.trim();
            const priceAddition = priceAdditions[i].value.trim();
            
            if (sizeName !== '' && priceAddition !== '') {
                sizes.push({
                    size_name: sizeName,
                    price_addition: parseFloat(priceAddition)
                });
            }
        }
        formData.append('sizes', JSON.stringify(sizes));

        const params = [];
        document.querySelectorAll('#params-container input').forEach(input => {
            if (input.value.trim() !== '') {
                params.push(input.value.trim());
            }
        });
        formData.append('params', JSON.stringify(params));

        const options = [];
        document.querySelectorAll('#options-container input').forEach(input => {
            if (input.value.trim() !== '') {
                options.push(input.value.trim());
            }
        });
        formData.append('options', JSON.stringify(options));

        // Определяем URL для отправки
        const url = 'save_good.php<?php echo ($red && !empty($good)) ? "?good_id=" . $_GET["id"] : ""; ?>';

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    const message = <?php echo ($red && !empty($good)) ? "'Товар успешно отредактирован'" : "'Товар успешно создан'"; ?>;
                    showMessage(message, 'success');

                    window.location.href = '/admin/goods.php';
                    // Если это создание, очищаем форму
                    if (!<?php echo ($red && !empty($good)) ? 'true' : 'false'; ?>) {
                        document.getElementById('create-form').reset();
                        document.getElementById('params-container').innerHTML = '';
                        document.getElementById('options-container').innerHTML = '';
                        document.getElementById('sizes-container').innerHTML = '';
                        addParam();
                        addOption();
                        addSize();
                    }
                } else {
                    showMessage('Ошибка: ' + result.error, 'error');
                }
            })
            .catch(error => {
                showMessage('Ошибка при отправке данных: ' + error, 'error');
            });
    });

    // Показать сообщение
    function showMessage(text, type) {
        const messageDiv = document.getElementById('message');
        messageDiv.textContent = text;
        messageDiv.className = `message ${type}`;

        setTimeout(() => {
            messageDiv.textContent = '';
            messageDiv.className = '';
        }, 5000);
    }
</script>
</body>
</html>