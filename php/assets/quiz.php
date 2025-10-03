<link rel="stylesheet" href="assets/css/quiz.css?v=1234">
<section class="quiz-section">
    <!-- Заголовок -->
    <div class="section-header">
        <h1 style="">Don't want to browse the catalog yourself?</h1>
        <p>Get a customized quote for your rollershutter in just 1 minute with our simple questionnaire</p>
    </div>

    <!-- Квиз -->
    <div class="quiz-container">
        <div class="quiz-content">
            <!-- Прогресс-бар -->
            <div class="progress-section">
                <div class="progress-label">
                    <span>Question</span>
                    <span class="question-counter" id="questionCounter">1 out of 8</span>
                </div>
                <div class="top-progress-container">
                    <div class="top-progress-bar" id="topProgressBar"></div>
                </div>
            </div>

            <!-- Секция вопроса -->
            <div class="question-section">
                <h2>Where do you plan to install the rollershutter?</h2>
                <div class="options-grid">
                    <div class="option-card">Window</div>
                    <div class="option-card">Door</div>
                    <div class="option-card">Garage</div>
                    <div class="option-card">Commercial entrance</div>
                    <div class="option-card">Not sure yet</div>
                </div>
            </div>

            <!-- Секция контактных данных -->
            <div class="contact-section" style="display: none;">
                <h2 id="contact-title">Please provide your contact details</h2>
                <div class="contact-fields">
                    <div class="contact-field name-field">
                        <label for="user-name">Your Name *</label>
                        <input type="text" id="user-name" placeholder="John Smith">
                    </div>
                    <div class="contact-field email-field" style="display: none;">
                        <label for="user-email">Email Address *</label>
                        <input type="email" id="user-email" placeholder="your.email@example.com">
                    </div>
                    <div class="contact-field phone-field" style="display: none;">
                        <label for="user-phone">Phone Number *</label>
                        <input type="tel" id="user-phone" placeholder="+1 (555) 123-4567">
                    </div>
                    <div class="contact-field whatsapp-field" style="display: none;">
                        <label for="user-whatsapp">WhatsApp Number *</label>
                        <input type="tel" id="user-whatsapp" placeholder="+1 (555) 123-4567">
                    </div>
                    <div class="contact-field telegram-field" style="display: none;">
                        <label for="user-telegram">Telegram Username *</label>
                        <input type="text" id="user-telegram" placeholder="@username">
                    </div>
                </div>
            </div>

            <!-- Навигация -->
            <div class="quiz-navigation">
                <div class="navigation-container">
                    <div class="navigation-instruction">
                        <img src="assets/image/click.svg" width="32px" height="32px" alt="click">
                        <p style="font-size: 18px" id="navigation-text">Select an answer to continue</p>
                    </div>
                    <div class="navigation-buttons">
                        <button class="btn btn-next" style="display: none" hidden disabled>Next</button>
                        <button class="btn btn-back">Back</button>
                        <button class="btn btn-submit" style="display: none;">Complete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let optionCards = document.querySelectorAll('.option-card');
    const nextButton = document.querySelector('.btn-next');
    const backButton = document.querySelector('.btn-back');
    const submitButton = document.querySelector('.btn-submit');
    const topProgressBar = document.getElementById('topProgressBar');
    const questionCounter = document.getElementById('questionCounter');
    const questionSection = document.querySelector('.question-section');
    const contactSection = document.querySelector('.contact-section');
    const contactTitle = document.getElementById('contact-title');
    const navigationText = document.getElementById('navigation-text');
    
    let currentQuestion = 1;
    const totalQuestions = 8;
    let userAnswers = [];
    let userEmail = '';
    let userPhone = '';
    let userName = '';
    let userWhatsApp = '';
    let userTelegram = '';
    
    // Функция для обновления прогресс-бара и счетчика
    function updateProgress() {
        const progressPercentage = (currentQuestion / totalQuestions) * 100;
        topProgressBar.style.width = `${progressPercentage}%`;
        questionCounter.textContent = `${currentQuestion} out of ${totalQuestions}`;
    }
    
    // Функция для обновления вариантов ответов
    function updateOptionCards() {
        optionCards = document.querySelectorAll('.option-card');
        
        optionCards.forEach(card => {
            card.addEventListener('click', function() {
                // Снимаем выделение со всех карточек
                optionCards.forEach(c => c.classList.remove('selected'));
                
                // Выделяем выбранную карточку
                this.classList.add('selected');
                
                // Сохраняем ответ пользователя
                userAnswers[currentQuestion - 1] = this.textContent;
                
                // Автоматически переходим к следующему вопросу через короткую задержку
                setTimeout(() => {
                    goToNextQuestion();
                }, 100);
            });
        });
    }
    
    // Функция перехода к следующему вопросу
    function goToNextQuestion() {
        if (currentQuestion < totalQuestions) {
            // Анимация исчезновения
            questionSection.classList.add('fade-out');
            contactSection.classList.add('fade-out');
            
            setTimeout(() => {
                currentQuestion++;
                
                // Обновляем прогресс и счетчик
                updateProgress();
                
                // Загружаем следующий вопрос
                updateQuestionContent(currentQuestion);
                
                // Анимация появления
                setTimeout(() => {
                    questionSection.classList.remove('fade-out');
                    contactSection.classList.remove('fade-out');
                }, 30);
                
            }, 100);
        }
    }
    
    // Функция перехода к предыдущему вопросу
    function goToPreviousQuestion() {
        if (currentQuestion > 1) {
            // Анимация исчезновения
            questionSection.classList.add('fade-out');
            contactSection.classList.add('fade-out');
            
            setTimeout(() => {
                currentQuestion--;
                
                // Обновляем прогресс и счетчик
                updateProgress();
                
                // Загружаем предыдущий вопрос
                updateQuestionContent(currentQuestion);
                
                // Восстанавливаем предыдущий выбор, если он был
                if (userAnswers[currentQuestion - 1] && currentQuestion !== totalQuestions) {
                    setTimeout(() => {
                        optionCards = document.querySelectorAll('.option-card');
                        optionCards.forEach(card => {
                            if (card.textContent === userAnswers[currentQuestion - 1]) {
                                card.classList.add('selected');
                            }
                        });
                    }, 50);
                }
                
                // Анимация появления
                setTimeout(() => {
                    questionSection.classList.remove('fade-out');
                    contactSection.classList.remove('fade-out');
                }, 50);
                
            }, 300);
        }
    }
    
    // Функция завершения квиза
    function completeQuiz() {
        // Собираем все данные формы
        const formData = {
            name: userName || 'Не указано',
            email: userEmail || 'Не указано',
            phone: userPhone || 'Не указано',
            whatsapp: userWhatsApp || 'Не указано',
            telegram: userTelegram || 'Не указано',
            preferred_contact: userAnswers[6] || 'Не указано',
            installation_location: userAnswers[0] || 'Не указано',
            width: userAnswers[1] || 'Не указано',
            height: userAnswers[2] || 'Не указано',
            control_system: userAnswers[3] || 'Не указано',
            material: userAnswers[4] || 'Не указано',
            installation_timeline: userAnswers[5] || 'Не указано',
            submission_date: new Date().toLocaleString()
        };

        // Показываем анимацию завершения
        showCompletionAnimation();
        
        // Отправляем данные на сервер
        sendQuizData(formData);
    }

    // Функция для показа анимации завершения
    function showCompletionAnimation() {
        const questionSection = document.querySelector('.question-section');
        
        questionSection.innerHTML = `
            <div class="completion-animation">
                <div class="success-checkmark">
                    <div class="check-icon">
                        <span class="icon-line line-tip"></span>
                        <span class="icon-line line-long"></span>
                        <div class="icon-circle"></div>
                    </div>
                </div>
                <h2>Thank You!</h2>
                <p class="completion-message">Your rollershutter quote request has been successfully submitted.</p>
                <div class="completion-details">
                    <p>We will contact you within 15 minutes with your personalized quote.</p>
                    <p>Reference number: <strong>RS${Date.now().toString().slice(-6)}</strong></p>
                </div>
                <button class="btn-restart" onclick="location.reload()">Start New Questionnaire</button>
            </div>
        `;
        
        questionSection.style.display = 'block';
        contactSection.style.display = 'none';
        questionSection.classList.remove('fade-out');
        
        // Скрываем кнопки навигации
        document.querySelector('.navigation-buttons').style.display = 'none';
        navigationText.style.display = 'none';
        document.querySelector('.navigation-instruction img').style.display = 'none';
    }

    // Функция отправки данных на сервер
    function sendQuizData(formData) {
        fetch('http://localhost:8900/mail/api', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Error sending email:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    
    // Функция для обновления содержимого вопроса
    function updateQuestionContent(questionNum) {
        const questionTitle = document.querySelector('.question-section h2');
        const optionsGrid = document.querySelector('.options-grid');
        
        // Массив вопросов и вариантов ответов
        const questions = [
            "Where do you plan to install the rollershutter?",
            "What is the approximate width of the opening?",
            "What is the approximate height of the opening?",
            "What type of control system do you prefer?",
            "What is your preferred material for the rollershutter?",
            "When do you plan to install the rollershutter?",
            "How would you prefer to be contacted?",
            "Please provide your contact details"
        ];
        
        const options = [
            ["Window", "Door", "Garage", "Commercial entrance", "Not sure yet"],
            ["Less than 1 meter", "1-2 meters", "2-3 meters", "More than 3 meters", "Not sure"],
            ["Less than 1.5 meters", "1.5-2.5 meters", "2.5-3.5 meters", "More than 3.5 meters", "Not sure"],
            ["Manual control", "Electric with remote", "Smart home integration", "Not sure yet"],
            ["Aluminum", "Steel", "PVC", "Wood effect", "Not sure"],
            ["Within 1 month", "Within 3 months", "Within 6 months", "Just exploring options"],
            ["Email", "Phone call", "WhatsApp", "Telegram"],
            []
        ];
        
        // Показываем/скрываем секции в зависимости от вопроса
        if (questionNum === totalQuestions) {
            // Последний вопрос - показываем контактную секцию
            questionSection.style.display = 'none';
            contactSection.style.display = 'block';
            navigationText.textContent = "Fill in your contact details and click Complete";
            
            // Показываем кнопку Complete и скрываем Next
            nextButton.style.display = 'none';
            submitButton.style.display = 'block';
            
            initializeContactSection();
        } else {
            // Обычные вопросы
            questionSection.style.display = 'block';
            contactSection.style.display = 'none';
            
            // Обновляем вопрос
            questionTitle.textContent = questions[questionNum - 1];
            
            // Обновляем инструкцию
            if (questionNum === totalQuestions - 1) {
                navigationText.textContent = "Final question - almost done!";
            } else {
                navigationText.textContent = "Select an answer to continue";
            }
            
            // Скрываем кнопку Complete и показываем Next (если нужно)
            nextButton.style.display = 'none';
            submitButton.style.display = 'none';
            
            // Очищаем и обновляем варианты ответов
            optionsGrid.innerHTML = '';
            if (options[questionNum - 1].length > 0) {
                options[questionNum - 1].forEach(option => {
                    const optionCard = document.createElement('div');
                    optionCard.className = 'option-card';
                    optionCard.textContent = option;
                    optionsGrid.appendChild(optionCard);
                });
            }
            
            // Обновляем обработчики событий
            updateOptionCards();
        }
        
        // Обновляем состояние кнопки "Назад"
        backButton.disabled = questionNum === 1;
    }
    
    // Функция для инициализации контактной секции на основе выбранного способа связи
    function initializeContactSection() {
        const selectedContactMethod = userAnswers[6]; // 7-й вопрос (индекс 6)
        
        // Сбрасываем значения
        userEmail = '';
        userPhone = '';
        userName = '';
        userWhatsApp = '';
        userTelegram = '';
        
        // Сбрасываем поля формы
        document.getElementById('user-email').value = '';
        document.getElementById('user-phone').value = '';
        document.getElementById('user-name').value = '';
        document.getElementById('user-whatsapp').value = '';
        document.getElementById('user-telegram').value = '';
        
        // Скрываем все поля сначала
        document.querySelectorAll('.contact-field').forEach(field => {
            field.style.display = 'none';
            field.classList.remove('show');
        });
        
        // Показываем поле имени всегда
        const nameField = document.querySelector('.name-field');
        nameField.style.display = 'block';
        setTimeout(() => nameField.classList.add('show'), 10);
        
        // Показываем соответствующие поля в зависимости от выбранного способа связи
        switch(selectedContactMethod) {
            case 'Email':
                contactTitle.textContent = "Please provide your contact details for email communication";
                showContactField('email');
                break;
            case 'Phone call':
                contactTitle.textContent = "Please provide your contact details for phone call";
                showContactField('phone');
                break;
            case 'WhatsApp':
                contactTitle.textContent = "Please provide your contact details for WhatsApp";
                showContactField('whatsapp');
                break;
            case 'Telegram':
                contactTitle.textContent = "Please provide your contact details for Telegram";
                showContactField('telegram');
                break;
            default:
                // Если по какой-то причине способ не выбран, показываем все поля
                contactTitle.textContent = "Please provide your contact details";
                showContactField('email');
                showContactField('phone');
                showContactField('whatsapp');
                showContactField('telegram');
        }
        
        // Обработчики для полей ввода
        document.getElementById('user-email').addEventListener('input', function() {
            userEmail = this.value;
            checkContactFormValidity();
        });
        
        document.getElementById('user-phone').addEventListener('input', function() {
            userPhone = this.value;
            checkContactFormValidity();
        });
        
        document.getElementById('user-name').addEventListener('input', function() {
            userName = this.value;
            checkContactFormValidity();
        });
        
        document.getElementById('user-whatsapp').addEventListener('input', function() {
            userWhatsApp = this.value;
            checkContactFormValidity();
        });
        
        document.getElementById('user-telegram').addEventListener('input', function() {
            userTelegram = this.value;
            checkContactFormValidity();
        });
        
        // Изначально деактивируем кнопку Complete
        submitButton.disabled = true;
    }
    
    // Функция для показа конкретного поля контакта
    function showContactField(fieldType) {
        const field = document.querySelector(`.${fieldType}-field`);
        if (field) {
            field.style.display = 'block';
            setTimeout(() => field.classList.add('show'), 10);
        }
    }
    
    // Функция проверки валидности контактной формы
    function checkContactFormValidity() {
        let isValid = true;
        const selectedContactMethod = userAnswers[6];
        
        // Проверяем обязательное поле имени
        if (!userName.trim()) {
            isValid = false;
        }
        
        // Проверяем поля в зависимости от выбранного способа связи
        switch(selectedContactMethod) {
            case 'Email':
                if (!validateEmail(userEmail)) {
                    isValid = false;
                }
                break;
            case 'Phone call':
                if (!validatePhone(userPhone)) {
                    isValid = false;
                }
                break;
            case 'WhatsApp':
                if (!validatePhone(userWhatsApp)) {
                    isValid = false;
                }
                break;
            case 'Telegram':
                if (!userTelegram.trim() || !userTelegram.startsWith('@')) {
                    isValid = false;
                }
                break;
        }
        
        // Активируем/деактивируем кнопку Complete
        submitButton.disabled = !isValid;
    }
    
    // Функции валидации
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    function validatePhone(phone) {
        return phone.trim().length >= 5;
    }
    
    // Обработка кнопки "Далее"
    nextButton.addEventListener('click', goToNextQuestion);
    
    // Обработка кнопки "Назад"
    backButton.addEventListener('click', goToPreviousQuestion);
    
    // Обработка кнопки "Complete"
    submitButton.addEventListener('click', completeQuiz);
    
    // Инициализация
    updateProgress();
    updateOptionCards();
});
</script>