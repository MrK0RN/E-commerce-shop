<style>
/* Стили для улучшения UX формы */
.form-control {
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
    outline: none;
}

.form-control.error {
    border-color: #f44336;
}

.submit-btn {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.submit-btn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

/* Анимация загрузки */
.submit-btn.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top: 2px solid #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<section class="contact" id="contact">
    <div class="container">
        <div class="section-title">
            <h2 style="color:white;"><font color="white">Have Questions? Contact Us!</font></h2>
            <p>Get in touch for a free consultation and quote</p>
        </div>
        <div class="contact-container">
            <div class="contact-info">
                <h3>Our Contact Information</h3>
                <p>Feel free to reach out to us with any questions about our roller shutters or to schedule a consultation.</p>
                <div class="contact-details">
                    <?php
                        include "modules/ContactUs.php";
                    ?>
                </div>
            </div>
            <div class="contact-form">
                <h3>Send Us a Message</h3>
                <form id="form">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Send Request</button>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('form');
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Собираем данные формы
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            message: document.getElementById('message').value,
            form_type: 'contact_form'
        };

        // Валидация
        if (!formData.name || !formData.email || !formData.phone) {
            showNotification('Please fill in all required fields', 'error');
            return;
        }

        if (!validateEmail(formData.email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }

        // Показываем индикатор загрузки
        const submitBtn = contactForm.querySelector('.submit-btn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        // Отправляем данные на Python API
        fetch('http://localhost:8900/mail/api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Thank you! Your message has been sent successfully. We will contact you soon.', 'success');
                contactForm.reset(); // Очищаем форму
            } else {
                showNotification('Sorry, there was an error sending your message. Please try again later.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please check your connection and try again.', 'error');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });

    // Функция валидации email
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Функция показа уведомлений
    function showNotification(message, type) {
        // Удаляем предыдущие уведомления
        const existingNotification = document.querySelector('.form-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Создаем новое уведомление
        const notification = document.createElement('div');
        notification.className = `form-notification ${type}`;
        notification.textContent = message;
        
        // Стили для уведомления
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 10000;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease-out;
        `;

        if (type === 'success') {
            notification.style.background = '#4CAF50';
        } else {
            notification.style.background = '#f44336';
        }

        // Добавляем в DOM
        document.body.appendChild(notification);

        // Автоматически скрываем через 5 секунд
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.animation = 'slideOut 0.3s ease-in';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    // Добавляем CSS анимации
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Добавляем обработчики для улучшения UX
    const formInputs = contactForm.querySelectorAll('input, textarea');
    formInputs.forEach(input => {
        // Показываем валидацию в реальном времени
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.style.borderColor = '#f44336';
            } else if (this.type === 'email' && this.value && !validateEmail(this.value)) {
                this.style.borderColor = '#f44336';
            } else {
                this.style.borderColor = '#4CAF50';
            }
        });

        // Убираем красную обводку при начале ввода
        input.addEventListener('input', function() {
            if (this.style.borderColor === 'rgb(244, 67, 54)') {
                this.style.borderColor = '';
            }
        });
    });
});
</script>
    