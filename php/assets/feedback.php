<!-- Testimonials -->
<section class="testimonials">
    <div class="container">
        <div class="section-title">
            <h2>What Our Customers Say</h2>
            <p>Hear from homeowners and business owners who chose our roller shutters</p>
        </div>
        <div class="testimonial-slider">
            <div class="testimonial-slide active">
                <div class="testimonial">
                    <img src="https://randomuser.me/api/portraits/women/45.jpg" alt="Sarah J." class="testimonial-avatar">
                    <p class="testimonial-text">"The roller shutters have made such a difference to our home. Not only do they look great, but we feel much more secure and our energy bills have noticeably decreased."</p>
                    <h4 class="testimonial-author">Sarah J.</h4>
                    <p class="testimonial-role">Homeowner, London</p>
                </div>
            </div>
            <div class="testimonial-slide">
                <div class="testimonial">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="David M." class="testimonial-avatar">
                    <p class="testimonial-text">"Excellent service from start to finish. The installation team was professional and the shutters have exceeded our expectations in terms of quality."</p>
                    <h4 class="testimonial-author">David M.</h4>
                    <p class="testimonial-role">Business Owner, Manchester</p>
                </div>
            </div>
            <div class="testimonial-slide">
                <div class="testimonial">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Emily R." class="testimonial-avatar">
                    <p class="testimonial-text">"The noise reduction from these shutters is incredible. We live on a busy street and finally can sleep peacefully. Worth every penny!"</p>
                    <h4 class="testimonial-author">Emily R.</h4>
                    <p class="testimonial-role">Homeowner, Birmingham</p>
                </div>
            </div>
            <div class="slider-controls">
                <button class="slider-prev"><i class="fas fa-chevron-left"></i></button>
                <div class="slider-dots"></div>
                <button class="slider-next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>

<style>
    /* Testimonials Slider Styles */
    .testimonial-slider {
        position: relative;
        overflow: hidden;
        margin: 0 auto;
        max-width: 800px;
    }
    
    .testimonial-slide {
        display: none;
        padding: 20px;
        text-align: center;
        transition: all 0.5s ease;
    }
    
    .testimonial-slide.active {
        display: block;
    }
    
    .testimonial {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .testimonial-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
    }
    
    .testimonial-text {
        font-style: italic;
        margin-bottom: 15px;
        color: #555;
    }
    
    .testimonial-author {
        margin-bottom: 5px;
        color: #333;
    }
    
    .testimonial-role {
        color: #777;
        font-size: 14px;
    }
    
    .slider-controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }
    
    .slider-dots {
        display: flex;
        justify-content: center;
        margin: 0 15px;
    }
    
    .slider-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ddd;
        margin: 0 5px;
        cursor: pointer;
    }
    
    .slider-dot.active {
        background: #333;
    }
    
    .slider-prev, .slider-next {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #333;
        padding: 5px 15px;
    }
    
    @media (max-width: 768px) {
        .testimonial-slide {
            padding: 10px;
        }
    }
</style>

<script>
    // Testimonials Slider
    document.addEventListener('DOMContentLoaded', function() {
        const testimonialSlides = document.querySelectorAll('.testimonial-slide');
        const dotsContainer = document.querySelector('.slider-dots');
        let currentTestimonial = 0;
        
        // Create dots
        testimonialSlides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('slider-dot');
            if(index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                goToTestimonial(index);
            });
            dotsContainer.appendChild(dot);
        });
        
        // Next/prev buttons
        document.querySelector('.slider-next').addEventListener('click', () => {
            currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        });
        
        document.querySelector('.slider-prev').addEventListener('click', () => {
            currentTestimonial = (currentTestimonial - 1 + testimonialSlides.length) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        });
        
        // Auto-rotate testimonials
        let testimonialInterval = setInterval(() => {
            currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
            showTestimonial(currentTestimonial);
        }, 5000);
        
        // Pause on hover
        const testimonialSlider = document.querySelector('.testimonial-slider');
        testimonialSlider.addEventListener('mouseenter', () => {
            clearInterval(testimonialInterval);
        });
        
        testimonialSlider.addEventListener('mouseleave', () => {
            testimonialInterval = setInterval(() => {
                currentTestimonial = (currentTestimonial + 1) % testimonialSlides.length;
                showTestimonial(currentTestimonial);
            }, 5000);
        });
        
        function showTestimonial(index) {
            testimonialSlides.forEach(slide => slide.classList.remove('active'));
            testimonialSlides[index].classList.add('active');
            
            // Update dots
            const dots = document.querySelectorAll('.slider-dot');
            dots.forEach(dot => dot.classList.remove('active'));
            dots[index].classList.add('active');
        }
        
        function goToTestimonial(index) {
            currentTestimonial = index;
            showTestimonial(currentTestimonial);
        }
    });
</script>