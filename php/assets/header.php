<?php
//ini_set('display_errors', '0');

$phone = pgQuery("SELECT * FROM contacts WHERE contact_name = 'phone' AND show_field = 'True';")[0];
$wh = pgQuery("SELECT * FROM contacts WHERE contact_name = 'work_hours' AND show_field = 'True';")[0];

$clean_phone = preg_replace('/[^0-9]/', '', $phone["contact_value"]);
?>

<link href="https://fonts.googleapis.com/css2?family=Geologica:wght@100..900&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/header.css?v=1">

<!-- Стили для pop-up -->
<style>
.callback-popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    justify-content: center;
    align-items: center;
}

.callback-popup.active {
    display: flex;
}

.callback-popup-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 400px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    font-family: 'Geologica', sans-serif;
}

.callback-popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.callback-popup-close:hover {
    background: #f5f5f5;
    color: #333;
}

.callback-popup-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
    text-align: center;
}

.callback-popup-subtitle {
    font-size: 14px;
    color: #666;
    margin-bottom: 25px;
    text-align: center;
    line-height: 1.4;
}

.callback-form-group {
    margin-bottom: 20px;
}

.callback-form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.callback-form-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 16px;
    font-family: 'Geologica', sans-serif;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.callback-form-input:focus {
    outline: none;
    border-color: #FF68B6;
}

.callback-form-submit {
    width: 100%;
    padding: 14px;
    background: #FF68B6;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Geologica', sans-serif;
    transition: background-color 0.3s ease;
}

.callback-form-submit:hover {
    background: #e55aa4;
}

.callback-form-submit:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.callback-message {
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
    font-size: 14px;
}

.callback-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.callback-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.callback-loading {
    display: none;
    text-align: center;
    margin: 10px 0;
}

.callback-loading.active {
    display: block;
}

.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #FF68B6;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 480px) {
    .callback-popup-content {
        margin: 20px;
        padding: 25px 20px;
    }
    
    .callback-popup-title {
        font-size: 20px;
    }
}
</style>

<div>
    <header class="modern-header_8as4bd">
        <!-- Блок 1: Логотип -->
        <div class="header-block_8as4bd logo-block_8as4bd">
            <a href="/" class="logo-link_8as4bd">
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDIiIGhlaWdodD0iNDIiIHZpZXdCb3g9IjAgMCA0MiA0MiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjQyIiBoZWlnaHQ9IjQyIiByeD0iOCIgZmlsbD0iI0ZGNjhCNiIvPgo8cGF0aCBkPSJNMjEgMTFMMjYgMTZMMjEgMjFMMTYgMTZMMjEgMTFaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMjYgMTZMMzEgMjFMMjYgMjZMMjEgMjFMMjYgMTZaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTYgMTZMMjEgMjFMMTYgMjZMMTEgMjFMMTYgMTZaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMjEgMjFMMjYgMjZMMjEgMzFMMTYgMjZMMjEgMjFaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K" alt="Логотип компании" class="logo-image_8as4bd">
                <span>Company</span>
            </a>
        </div>

        <!-- Блок 2: Отображение на картах -->
        <a href="https://maps.google.com/" target="_blank" class="header-block_8as4bd maps-block_8as4bd">
            <div class="maps-content_8as4bd">
                <img width="27px" height="27px" src="/assets/image/googleMaps.png">
                <div class="maps-text_8as4bd">
                    <span>View on</span>
                    <span>Google Maps</span>
                </div>
            </div>
        </a>

        <!-- Блок 3: Рейтинг отзовиков -->
        <div class="header-block_8as4bd rating-block_8as4bd">
            <div class="rating-content_8as4bd">
                <span class="material-icons rating-stars">star</span>
                <span>4.8</span>
            </div>
            <div class="rating-text_8as4bd">
                <span class="rating-subtext_8as4bd">Google Reviews</span>
                <a href="#" class="reviews-link_8as4bd">Read Reviews</a>
            </div>
        </div>

        <!-- Блок 4: Контакт через WhatsApp -->
        <a href="https://wa.me/<?=$clean_phone?>" target="_blank" class="header-block_8as4bd wa-block_8as4bd">
            <div class="wa-content_8as4bd">
                <img width="27px" height="27px" src="/assets/image/whatsapp.png">
                <div class="wa-text_8as4bd">
                    <span class="wa-subtext_8as4bd">Any questions?</span>
                    <span class="wa-link_8as4bd">Message WhatsApp</span>
                </div>
            </div>
        </a>

        <!-- Блок 5: Режим работы и телефон -->
        <div class="header-block_8as4bd contacts-block_8as4bd">
            <div class="contacts-text_8as4bd">
                <span class="work-hours_8as4bd"><?=$wh["contact_value"]?></span>
                <a href="tel:+<?=$clean_phone?>" class="phone-link_8as4bd"><?=$phone["contact_value"]?></a>
                <a href="#" class="callback-link_8as4bd" id="callbackLink">Shall we call you?</a>
            </div>
        </div>

        <!-- Блок 6: Кнопка CTA -->
        <div class="header-block_8as4bd cta-block_8as4bd" href="/shop/catalog.php">
            <a href="/shop/catalog.php" class="cta-button_8as4bd">Go Ahead</a>
        </div>

        <!-- Блок 7: Мобильное меню -->
        <div class="header-block_8as4bd mobile-menu-block_8as4bd">
            <button class="mobile-menu-toggle_8as4bd" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Pop-up форма -->
    <div class="callback-popup" id="callbackPopup">
        <div class="callback-popup-content">
            <button class="callback-popup-close" id="callbackClose">&times;</button>
            
            <h3 class="callback-popup-title">Shall we call you?</h3>
            <p class="callback-popup-subtitle">Leave your phone number and we will call you back within 15 minutes</p>
            
            <form id="callbackForm" class="callback-form">
                <div id="callbackMessage" class="callback-message" style="display: none;"></div>
                
                <div class="callback-form-group">
                    <label class="callback-form-label" for="callbackName">Your Name</label>
                    <input type="text" id="callbackName" name="name" class="callback-form-input" placeholder="Enter your name" required>
                </div>
                
                <div class="callback-form-group">
                    <label class="callback-form-label" for="callbackPhone">Phone Number</label>
                    <input type="tel" id="callbackPhone" name="phone" class="callback-form-input" placeholder="+1 (123) 456-7890" required>
                </div>
                
                <div class="callback-form-group">
                    <label class="callback-form-label" for="callbackMessageText">Message (optional)</label>
                    <textarea id="callbackMessageText" name="message" class="callback-form-input" placeholder="Any additional information..." rows="3"></textarea>
                </div>
                
                <div class="callback-loading" id="callbackLoading">
                    <div class="loading-spinner"></div>
                    <p>Sending...</p>
                </div>
                
                <button type="submit" class="callback-form-submit" id="callbackSubmit">
                    Request a Call
                </button>
            </form>
        </div>
    </div>

    <script>
        // Класс для управления callback pop-up
        class CallbackPopup {
            constructor() {
                this.popup = document.getElementById('callbackPopup');
                this.openLink = document.getElementById('callbackLink');
                this.closeBtn = document.getElementById('callbackClose');
                this.form = document.getElementById('callbackForm');
                this.submitBtn = document.getElementById('callbackSubmit');
                this.loading = document.getElementById('callbackLoading');
                this.messageDiv = document.getElementById('callbackMessage');
                
                this.init();
            }

            init() {
                // Открытие pop-up
                this.openLink.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.open();
                });

                // Закрытие pop-up
                this.closeBtn.addEventListener('click', () => {
                    this.close();
                });

                // Закрытие при клике вне формы
                this.popup.addEventListener('click', (e) => {
                    if (e.target === this.popup) {
                        this.close();
                    }
                });

                // Обработка отправки формы
                this.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.submitForm();
                });

                // Закрытие по ESC
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.popup.classList.contains('active')) {
                        this.close();
                    }
                });
            }

            open() {
                this.popup.classList.add('active');
                document.body.style.overflow = 'hidden';
                this.clearForm();
            }

            close() {
                this.popup.classList.remove('active');
                document.body.style.overflow = '';
            }

            clearForm() {
                this.form.reset();
                this.hideMessage();
            }

            showMessage(text, type) {
                this.messageDiv.textContent = text;
                this.messageDiv.className = `callback-message ${type}`;
                this.messageDiv.style.display = 'block';
            }

            hideMessage() {
                this.messageDiv.style.display = 'none';
            }

            async submitForm() {
                const formData = new FormData(this.form);
                const data = {
                    name: formData.get('name'),
                    phone: formData.get('phone'),
                    message: formData.get('message'),
                    timestamp: new Date().toISOString()
                };

                // Валидация
                if (!data.name || !data.phone) {
                    this.showMessage('Please fill in all required fields', 'error');
                    return;
                }

                // Показать загрузку
                this.loading.classList.add('active');
                this.submitBtn.disabled = true;
                this.hideMessage();

                try {
                    const response = await fetch('http://localhost:8900/mail/api', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    });

                    if (response.ok) {
                        this.showMessage('Thank you! We will call you back soon.', 'success');
                        this.form.reset();
                        
                        // Автоматическое закрытие через 3 секунды
                        setTimeout(() => {
                            this.close();
                        }, 3000);
                    } else {
                        throw new Error('Server error');
                    }
                } catch (error) {
                    console.error('Error submitting form:', error);
                    this.showMessage('Sorry, there was an error. Please try again later or call us directly.', 'error');
                } finally {
                    this.loading.classList.remove('active');
                    this.submitBtn.disabled = false;
                }
            }
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', () => {
            new CallbackPopup();
            
            // Инициализация остальных классов
            new NavigationHeader();
            new DynamicGap();
            new MobileMenu();
        });

        // Остальной существующий JavaScript код...
        class NavigationHeader {
			constructor() {
				this.navHeader = document.getElementById('navHeader');
				this.lastScrollY = window.scrollY;
				this.scrolling = false;
				this.isFixed = false;
				
				this.init();
			}

			init() {
				// Инициализация позиции
				this.updatePosition();
				
				// Обработчик скролла
				window.addEventListener('scroll', () => {
					if (!this.scrolling) {
						this.scrolling = true;
						requestAnimationFrame(() => {
							this.handleScroll();
							this.scrolling = false;
							this.showHeader();
						});
					}
				});

				// Обработчик изменения размера окна
				window.addEventListener('resize', () => {
					this.updatePosition();
				});

				// Активный пункт меню при загрузке
				this.setActiveMenuItem();
			}

			handleScroll() {
				const currentScrollY = window.scrollY;
				const scrollThreshold = 50;
				
				// Фиксация хедера при достижении определенной точки
				if (currentScrollY > 120 && !this.isFixed) {
					this.navHeader.style.position = 'fixed';
					this.navHeader.style.top = '0';
					this.isFixed = true;
				} else if (currentScrollY <= 120 && this.isFixed) {
					this.navHeader.style.position = 'absolute';
					this.navHeader.style.top = '120px';
					this.isFixed = false;
				}

				// Изменение стиля при скролле
				if (currentScrollY > scrollThreshold) {
					this.navHeader.classList.remove('transparent');
					this.navHeader.classList.add('scrolled');
				} else {
					this.navHeader.classList.remove('scrolled');
					this.navHeader.classList.add('transparent');
				}

				// Показ/скрытие при направлении скролла
				if (currentScrollY > this.lastScrollY && currentScrollY > 200) {
					this.hideHeader();
				} else if (currentScrollY <= this.lastScrollY || currentScrollY <= 200) {
					this.showHeader();
				}

				this.lastScrollY = currentScrollY;
				this.setActiveMenuItem();
			}

			updatePosition() {
				if (window.scrollY <= 120) {
					this.navHeader.style.position = 'absolute';
					this.navHeader.style.top = '120px';
					this.isFixed = false;
				} else {
					this.navHeader.style.position = 'fixed';
					this.navHeader.style.top = '0';
					this.isFixed = true;
				}
			}

			hideHeader() {
				if (this.isFixed) {
					this.navHeader.style.transform = 'translateY(-100%)';
					this.navHeader.style.transition = 'transform 0.3s ease';
				}
			}

			showHeader() {
				this.navHeader.style.transform = 'translateY(0)';
				this.navHeader.style.transition = 'transform 0.3s ease';
			}

			setActiveMenuItem() {
				// Логика для установки активного пункта меню на основе скролла
				const sections = document.querySelectorAll('section');
				const navLinks = document.querySelectorAll('.nav-link_8as4bd');
				
				let currentSection = '';
				sections.forEach(section => {
					const sectionTop = section.offsetTop - 100;
					if (window.scrollY >= sectionTop) {
						currentSection = section.getAttribute('id');
					}
				});

				navLinks.forEach(link => {
					link.parentElement.classList.remove('active');
					if (link.getAttribute('href') === `#${currentSection}`) {
						link.parentElement.classList.add('active');
					}
				});
			}
		}

        class DynamicGap {
			constructor() {
				this.header = document.querySelector('.modern-header_8as4bd');
				this.lastWidth = window.innerWidth;
				
				this.init();
			}

			init() {
				// Обработчик изменения размера окна
				window.addEventListener('resize', () => {
					this.handleResize();
				});

				// Инициализация при загрузке
				this.updateGap();
			}

			handleResize() {   
				const currentWidth = window.innerWidth;
				
				// Обновляем gap только если ширина изменилась
				if (currentWidth !== this.lastWidth) {
					this.updateGap();
					this.lastWidth = currentWidth;
				}
			}

			updateGap() {
				if (!this.header) return;

				const width = window.innerWidth;
				
				if (width < 1200) {
					// Динамический расчет gap на основе ширины экрана
					let gap;
					
					if (width >= 1101) {
						gap = 23 + (width - 1100)/10; // Максимальный gap для больших экранов
					} else if (width >= 1001) {
					gap = 23 + (width - 1000)/8; // Средний gap
					} else if (width >= 901) {
					gap = 10 + (width - 900)/6; // Уменьшенный gap
					} else if (width >= 841) {
					gap = 10 + (width - 900)/6; // Еще меньше
					} else if (width >= 701) {
					gap = 50 + (width - 700)/5;
					} else {
						gap = 5; // Очень маленький gap для мобильных
					}
					
					this.header.style.gap = `${gap}px`;
				} else {
					// Сбрасываем gap для больших экранов
					this.header.style.gap = '';
				}
			}
			}


		class MobileMenu {
			constructor() {
				this.mobileMenuToggle = document.getElementById('mobileMenuToggle');
				this.mobileMenu = document.getElementById('mobileMenu');
				this.isOpen = false;
				
				this.init();
			}

			init() {
				this.mobileMenuToggle.addEventListener('click', () => {
					this.toggleMenu();
				});

				// Закрытие меню при клике на ссылку
				const mobileLinks = this.mobileMenu.querySelectorAll('.nav-link_8as4bd');
				mobileLinks.forEach(link => {
					link.addEventListener('click', () => {
						this.closeMenu();
					});
				});

				// Закрытие меню при клике вне его
				this.mobileMenu.addEventListener('click', (e) => {
					if (e.target === this.mobileMenu) {
						this.closeMenu();
					}
				});

				// Закрытие меню при изменении размера экрана
				window.addEventListener('resize', () => {
					if (window.innerWidth > 700) {
						this.closeMenu();
					}
				});
			}

			toggleMenu() {
				this.isOpen = !this.isOpen;
				
				if (this.isOpen) {
					this.openMenu();
				} else {
					this.closeMenu();
				}
			}

			openMenu() {
				this.mobileMenu.classList.add('active');
				this.mobileMenuToggle.classList.add('active');
				document.body.style.overflow = 'hidden';
				this.isOpen = true;
			}

			closeMenu() {
				this.mobileMenu.classList.remove('active');
				this.mobileMenuToggle.classList.remove('active');
				document.body.style.overflow = '';
				this.isOpen = false;
			}
		}  // ... существующий код MobileMenu

		document.querySelectorAll('.nav-link_8as4bd').forEach(link => {
			link.addEventListener('click', function(e) {
				e.preventDefault();
				const targetId = this.getAttribute('href');
				if (targetId.charAt(0) == "#"){
                    const targetElement = document.querySelector(targetId);
				
                    if (targetElement) {
                        const offsetTop = targetElement.offsetTop - 80;
                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
					}
				} else {
					window.location.href = targetId;
				}
                });
			});

        // Дополнительная логика для touch-устройств
        let touchStartY = 0;
        let touchEndY = 0;

        document.addEventListener('touchstart', e => {
            touchStartY = e.changedTouches[0].screenY;
        });

        document.addEventListener('touchend', e => {
            touchEndY = e.changedTouches[0].screenY;
            handleTouchMove();
        });

        function handleTouchMove() {
            if (touchStartY - touchEndY > 50) {
                document.getElementById('navHeader').style.transform = 'translateY(-100%)';
            } else if (touchEndY - touchStartY > 50) {
                document.getElementById('navHeader').style.transform = 'translateY(0)';
            }
        }
    </script>
</div>