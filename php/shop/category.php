<?php


include "../system/db.php";
$responces = pgQuery('SELECT id FROM goods;');
$jsonData = json_encode($responces, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$categoryName = !empty($categoryData) ? $categoryData[0]['name'] : 'Категория';
$categoryDescription = !empty($categoryData) ? $categoryData[0]['description'] : '';
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Рольставни — <?php echo htmlspecialchars($categoryName); ?></title>
    <script>
        // Безопасная передача данных из PHP в JS
        window.phpData = <?php echo $jsonData; ?>;
    </script>
    <style>
        :root{
            --card-w:300px;          /* ширина карточки */
            --ratio:1.6;              /* 8/5=1.6 */
            --card-h:calc(var(--card-w) * var(--ratio));
            --accent:#ff6b00;
            --gray:#777;
        }
        *{box-sizing:border-box;margin:0;padding:0;}
        body{
            background: white;
        }

        /* ==========  сама карточка  ========== */
        .card{
            width:var(--card-w);
            height:var(--card-h);
            background:#fff;
            border-radius:12px;
            box-shadow:0 4px 18px rgba(0,0,0,.10);
            display:flex;
            flex-direction:column;
            overflow:hidden;
            position:relative;
            margin-right: 4%;
        }

        /* 1. карусель 5:8 */
        .carousel{height:48%;position:relative;}
        .carousel .big{
            width:100%;height:100%;
            object-fit:cover;
            display:block;
        }
        /* полоски-переключатели */
        .triggers{
            position:absolute;bottom:0;left:0;right:0;
            height:28px;display:flex;
        }
        .triggers label{flex:1;height:100%;cursor:pointer;}
        /* сами превью под курсором */
        .triggers label::after{
            content:'';display:block;width:100%;height:100%;
            background-size:cover;background-position:center;filter:brightness(.85);
            transition:filter .15s;
        }
        .triggers label:nth-of-type(1)::after{background-image:var(--i1);}
        .triggers label:nth-of-type(2)::after{background-image:var(--i2);}
        .triggers label:nth-of-type(3)::after{background-image:var(--i3);}
        .triggers label:nth-of-type(4)::after{background-image:var(--i4);}
        .triggers label:hover::after{filter:brightness(1);}

        /* радиокнопки управляют картинкой */
        .carousel input{display:none;}
        .carousel input:nth-of-type(1):checked ~ .big{content:var(--i1);}
        .carousel input:nth-of-type(2):checked ~ .big{content:var(--i2);}
        .carousel input:nth-of-type(3):checked ~ .big{content:var(--i3);}
        .carousel input:nth-of-type(4):checked ~ .big{content:var(--i4);}
        /* fallback для старых браузеров – просто ставим background */
        .carousel .big{background-size:cover;background-position:center;}
        .carousel input:nth-of-type(1):checked ~ .big{background-image:var(--i1);}
        .carousel input:nth-of-type(2):checked ~ .big{background-image:var(--i2);}
        .carousel input:nth-of-type(3):checked ~ .big{background-image:var(--i3);}
        .carousel input:nth-of-type(4):checked ~ .big{background-image:var(--i4);}

        /* 2. название */
        .title{padding:10px 14px 4px;font-size:17px;font-weight:700;line-height:1.25;}

        /* 3. рейтинг */
        .rating{padding:0 14px;display:flex;align-items:center;gap:6px;font-size:14px;color:var(--gray);}
        .stars{color:#ffa723;font-size:15px;letter-spacing:1px;}

        /* 4. цвета */
        .colors{padding:8px 14px;display:flex;align-items:center;gap:8px;}
        .color{
            width:20px;height:20px;border-radius:50%;
            border:1px solid rgba(0,0,0,.15);
        }
        .more{color:var(--gray);font-size:13px;margin-left:4px;}

        /* 5-6. цены */
        .prices{padding:4px 14px 8px;display:flex;align-items:baseline;gap:8px;}
        .old{text-decoration:line-through;color:var(--gray);font-size:14px;}
        .current{font-size:22px;font-weight:700;color:var(--accent);}

        /* 7. доставка */
        .delivery{
            margin-top:auto;
            padding:10px 14px 14px;
            font-size:13px;color:var(--gray);
        }
        .delivery b{color:#222;}
        .image-container{
        position:relative;
        width:100%;
        height:100vh;
        aspect-ratio: 300/284;   /* стартовое фото */
        background-size:cover;
        background-position:center;                                /* кол-во зон */
        }
        .hover-zone{
        position:absolute;
        top:0;
        height:100%;
        background:transparent;
        cursor:pointer;
        }
        /* ========== Заголовок категории ========== */
        .category-header {
            margin-bottom: 40px;
            max-width: 1200px;
            margin: 0 auto 30px;
            padding: 0 40px;
            justify-content: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .category-title {
            font-size: 32px;
            font-weight: 700;
            color: #222;
            margin-bottom: 15px;
        }

        .category-description {
            font-size: 16px;
            line-height: 1.6;
            color: var(--gray);
            max-width: 800px;
        }
    </style>
    
</head>
<body style="padding-top: 200px">
    <?php
    include "../assets/header_black.php"
    ?>
    <div class="category-header">
        <h1 class="category-title">Catalog</h1>
    </div>
    <div style="max-width: 1200px;
                margin: 0 auto 30px;
                padding: 0 40px;
                display: flex;
                justify-content: left;" id="category-container"></div>

    <script>
    const apiBase = 'api/renderCards.php';
    
    // Функция для инициализации hover-эффекта
    function initImageHover(container, id) {
        let image_list = document.getElementById('fileCount_' + id).value.split('|;');
        const n = image_list.length;
        let images = [];
        
        for (let i = 0; i < n; i++) {
            const image = '/data/images/' + id + '/' + image_list[i];
            images.push(image);
            console.log(image);
        }
        
        // Устанавливаем начальное изображение
        container.style.backgroundImage = `url('${images[0]}')`;
        
        // Создаём зоны
        for (let i = 0; i < n; i++) {
            const zone = document.createElement('div');
            zone.className = 'hover-zone';
            zone.style.width = `${100 / n}%`;
            zone.style.left = `${(100 / n) * i}%`;
            zone.addEventListener('mouseenter', () => {
                container.style.backgroundImage = `url('${images[i]}')`;
            });
            container.appendChild(zone);
        }

        // Возвращаем стартовое фото
        container.addEventListener('mouseleave', () => {
            container.style.backgroundImage = `url('${images[0]}')`;
        });
    }

    async function loadEach(id){
        try{
            const res = await fetch(`${apiBase}?id=${id}`, { credentials: 'same-origin' });
            if(!res.ok){
                reachedEnd = true;
            }
            const html = (await res.text()).trim();
            if(!html){
                reachedEnd = true;
            }
            const wrapper = document.createElement('div');
            wrapper.className = 'card';
            wrapper.addEventListener('click', function() {
                window.location.href = 'card.php?id=' + id;
            });
            wrapper.innerHTML = html;
            document.getElementById('category-container').appendChild(wrapper);
            
            // Инициализируем hover-эффект после добавления карточки
            const container = document.getElementById('imageContainer-' + id);
            if (container) {
                initImageHover(container, id);
            }
        }catch(e){
            reachedEnd = true;
        }
    }

    (function(){
        const sentinel = document.getElementById('sentinel');
        const apiBase = 'api/renderCards.php';
        let nextId = 1;
        let isLoading = false;
        let reachedEnd = false;
        let each_batch = 1;
        
        window.phpData.forEach((item, index) => {
            loadEach(item["id"]);
        })
    })();
    </script>
    <?php
	include "../assets/brands.php";
	include "../assets/footer.php";
	?>
</body>
</html>