FROM python:3.11-slim

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    gcc \
    python3-dev \
    && rm -rf /var/lib/apt/lists/*

# Создаем директорию приложения
WORKDIR /app

# Копируем requirements первыми для кэширования
COPY requirements.txt .

# Устанавливаем Python зависимости
RUN pip install --no-cache-dir -r requirements.txt

# Копируем остальные файлы
COPY . .

# Создаем необходимые директории
RUN mkdir -p /app/data /app/mail/quotes \
    && chmod 755 /app/data /app/mail/quotes

# Открываем порт
EXPOSE 8900

# Запускаем приложение
CMD ["python", "app.py"]