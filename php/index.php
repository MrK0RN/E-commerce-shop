<?php
function getAllPhotos() {
    $query = "SELECT * FROM photos ORDER BY created_at DESC";
    $result = pgQuery($query);
    
    $photos = [];
    foreach ($result as $row) {
        $photos[] = $row;
    }
    
    return $photos;
}
include "system/db.php";
$query = "SELECT * FROM goods ORDER BY created_at DESC";
$result = pgQuery($query);
$files = [];
$ids = [];
foreach ($result as $id){
    $dir = "data/images/".$id['id']."/";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Roller Shutters | Security & Style</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css?v=1234">
    <link rel="stylesheet" href="assets/css/catalog_ind.css?v=1234">
    <link href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
        <?php
            include "assets/navHeader.php";
        ?>
    <!-- Hero Section -->
    <section class="hero" id="home">
        <?php
            include "assets/header.php";
        ?>
        <div class="hero-content">
            <h1>Premium <span style="color: var(--accent)">Roller Shutters</span> for Your <span style="color: var(--accent)">Home</span> & <span style="color: var(--accent)">Business</span></h1>
            <p>Security, privacy and style in one complete solution</p>
            <div>
                <a href="shop/catalog.php" class="btn">Check Our Catalog</a>
                <a href="#gallery" class="btn btn-secondary">View Gallery</a>
            </div>
        </div>
    </section>
    <section class="catalog-section">
        <div class="container">
            <div class="catalog-grid">
            <?php foreach ($files as $key => $value): ?>
                <div class="catalog-card" onclick="window.location.href='shop/category.php?id=<?=$key?>'">
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
    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <img width=90px height=90px src="assets/image/docs.png"></img>
                    </div>
                    <p><b>Enhanced Security</b><br><font size="2">Protection against break-ins and prying eyes with reinforced material</font></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <img width=100px height=90px src="assets/image/energy.webp"></img>
                    </div>
                    <p><b>Energy Efficiency</b><br><font size="2">Reduce heat loss in winter and block excessive heat in summer</font></p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <img width=100px height=90px src="assets/image/Lupa.png"></img>
                    </div>
                    <p><b>Quality Check</b><br><font size="2">Contemporary solutions to match any architectural style</font></p>
                </div>
            </div>
        </div>
    </section>
    <?php
    include "assets/quiz.php";
    ?>
    <section class="gallery" id="gallery">
        <div class="container">
            <div class="section-title">
                <h2>Our Projects</h2>
                <p>See our roller shutters installed in real homes and businesses</p>
            </div>
            <div class="gallery-container">
    <?php
    $photos = getAllPhotos();
    define('UPLOAD_DIR', 'data/projects/images');
        if (empty($photos)) {
            $main .= '<p>Пока нет загруженных фотографий.</p>';
        } else {
            foreach ($photos as $photo) {
                $main = '
                    <div class="gallery-item">
                        <img src="' . UPLOAD_DIR . htmlspecialchars($photo['filename']) . '" 
                            alt="' . htmlspecialchars($photo['description']) . '">
                        <div class="gallery-caption">
                            <h3>' . htmlspecialchars($photo['description']) . '</h3>
                        </div>
                        </div> 
                ';
                echo $main;
            }
        }
    ?>
            </div>
            <div class="gallery-btn">
                <a href="shop/catalog.php" class="btn">Get a Free Quote</a>
            </div>
        </div>
    </section>
    <!-- FAQ Section -->
    
    
    <!-- Testimonials -->
    <?php
	include "assets/feedback.php";
	?>
    
    <!-- Brands -->
    <?php
	include "assets/brands.php";
	?>
    
    <!-- Contact Section -->
    <?php
	include "assets/contactUs.php";
	?>

    <section class="faq" id="faq">
        <div class="container">
            <div class="section-title">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about our roller shutters</p>
            </div>
            <div class="accordion">
                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>How to choose the right roller shutters?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Consider factors like security needs, insulation requirements, window/door size, and your budget. Our experts can help you select the perfect model based on your specific needs and property characteristics.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>How long does installation take?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Standard installation typically takes 2-4 hours per window, depending on the complexity. For larger projects or custom solutions, it may take longer. We'll provide an accurate timeline during your free consultation.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>Which material is better: aluminum or steel?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Aluminum is lightweight, corrosion-resistant and more affordable, while steel offers superior strength and security. For most residential applications, aluminum is sufficient. For high-security needs, we recommend steel shutters.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>Is there a warranty?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Yes, we offer a 5-year warranty on materials and a 2-year warranty on installation work. Some premium products come with extended warranties up to 10 years.</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>How to place an order?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="accordion-content">
                        <p>Simply contact us via phone, email, or the contact form on this website to schedule a free consultation. We'll visit your property, take measurements, discuss options, and provide a detailed quote.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Enhance Your Property?</h2>
            <p>Get premium roller shutters that combine security, energy efficiency and elegant design in one solution.</p>
            <a href="shop/catalog.php" class="btn">Get Your Free Quote Now</a>
        </div>
    </section>
    
    <!-- Footer -->
    <?php
	    include "assets/footer.php";
	?>
    
    <!-- Chat Widget -->
    <div class="chat-widget">
        <div class="chat-btn">
            <i class="fas fa-comment-dots"></i>
        </div>
    </div>
    
    <script>
        // Mobile Menu Toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');
        
        /*
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            mobileMenuBtn.innerHTML = navLinks.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        */
        // Smooth Scrolling for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                if(this.getAttribute('href') === '#') return;
                
                const target = document.querySelector(this.getAttribute('href'));
                if(target) {
                    window.scrollTo({
                        top: target.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    if(navLinks.classList.contains('active')) {
                        navLinks.classList.remove('active');
                        mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                    }
                }
            });
        });
        
        // Header Scroll Effect
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => {
            if(window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Accordion FAQ
        const accordionItems = document.querySelectorAll('.accordion-item');
        accordionItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            header.addEventListener('click', () => {
                item.classList.toggle('active');
                
                // Close other open items
                accordionItems.forEach(otherItem => {
                    if(otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                    }
                });
            });
        });
        
        // Countdown Timer
        function updateCountdown() {
            const now = new Date();
            const endOfDay = new Date();
            endOfDay.setHours(23, 59, 59, 999);
            
            const diff = endOfDay - now;
            
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            document.getElementById('countdown').textContent = 
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        setInterval(updateCountdown, 1000);
        updateCountdown();
        
        // Form Submission
        const form = document.getElementById('form');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            // Here you would typically send the form data to your server
            alert('Thank you for your message! We will contact you shortly.');
            form.reset();
        });
        
        // Gallery Hover Effect
        const galleryItems = document.querySelectorAll('.gallery-item');
        galleryItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                item.querySelector('.gallery-caption').style.transform = 'translateY(0)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.querySelector('.gallery-caption').style.transform = 'translateY(100%)';
            });
        });
        
        // Chat Widget
        const chatBtn = document.querySelector('.chat-btn');
        chatBtn.addEventListener('click', () => {
            alert('Chat service will open in a real implementation. For now, please use our contact form or call us.');
        });
    </script>
</body>
</html>
