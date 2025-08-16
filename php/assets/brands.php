<!-- Brands -->
<section class="brands">
    <div class="container">
        <div class="section-title">
            <h2>Trusted by Leading Brands</h2>
            <p>We work with the best manufacturers in the industry</p>
        </div>
        <div class="brands-slider">
            <div class="brands-track">
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+1" alt="Brand 1" class="brand-logo">
                </div>
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+2" alt="Brand 2" class="brand-logo">
                </div>
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+3" alt="Brand 3" class="brand-logo">
                </div>
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+4" alt="Brand 4" class="brand-logo">
                </div>
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+5" alt="Brand 5" class="brand-logo">
                </div>
                <div class="brand-slide">
                    <img src="https://via.placeholder.com/150x60?text=Brand+6" alt="Brand 6" class="brand-logo">
                </div>
            </div>
            <div class="slider-controls">
                <button class="brands-prev"><i class="fas fa-chevron-left"></i></button>
                <button class="brands-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>

<style>
    /* Brands Slider Styles */
    .brands-slider {
        overflow: hidden;
        position: relative;
        padding: 0 40px;
    }
    
    .brands-track {
        display: flex;
        transition: transform 0.5s ease;
    }
    
    .brand-slide {
        flex: 0 0 25%;
        padding: 0 15px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .brand-logo {
        max-width: 100%;
        height: auto;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .brand-logo:hover {
        filter: grayscale(0);
        opacity: 1;
    }
    
    .brands .slider-controls {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    
    .brands-prev, .brands-next {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #333;
        padding: 5px 15px;
    }
    
    @media (max-width: 768px) {
        .brand-slide {
            flex: 0 0 50%;
        }
    }
    
    @media (max-width: 480px) {
        .brand-slide {
            flex: 0 0 100%;
        }
    }
</style>

<script>
    // Brands Slider
    document.addEventListener('DOMContentLoaded', function() {
        const brandsTrack = document.querySelector('.brands-track');
        const brandSlides = document.querySelectorAll('.brand-slide');
        const brandsPrev = document.querySelector('.brands-prev');
        const brandsNext = document.querySelector('.brands-next');
        let currentBrand = 0;
        const slideWidth = brandSlides[0].getBoundingClientRect().width;
        const visibleSlides = window.innerWidth > 768 ? 4 : (window.innerWidth > 480 ? 2 : 1);
        
        // Position brands track
        brandsTrack.style.transform = `translateX(0)`;
        
        // Next/prev buttons
        brandsNext.addEventListener('click', () => {
            if(currentBrand < brandSlides.length - visibleSlides) {
                currentBrand++;
                brandsTrack.style.transform = `translateX(-${currentBrand * slideWidth}px)`;
            }
        });
        
        brandsPrev.addEventListener('click', () => {
            if(currentBrand > 0) {
                currentBrand--;
                brandsTrack.style.transform = `translateX(-${currentBrand * slideWidth}px)`;
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            const newVisibleSlides = window.innerWidth > 768 ? 4 : (window.innerWidth > 480 ? 2 : 1);
            if(newVisibleSlides !== visibleSlides) {
                currentBrand = 0;
                brandsTrack.style.transform = `translateX(0)`;
            }
        });
    });
</script>