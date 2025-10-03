from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import os
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from datetime import datetime
import logging

app = Flask(__name__)
CORS(app)

# Конфигурация
SMTP_CONFIG = {
    'host': 'smtp.gmail.com',
    'port': 587,
    'username': 'aaakrasnykh@gmail.com',
    'password': 'mbph ujko ssmh uzln',  # Замените на пароль приложения
    'from_email': 'aaakrasnykh@gmail.com',
    'from_name': 'Website Forms',
    'to_email': 'aaakrasnykh@gmail.com',
    'to_name': 'Alexander Krasnykh'
}

# Настройка логирования
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def get_form_type_and_subject(data):
    """Определяет тип формы и генерирует тему письма"""
    if 'installation_location' in data:
        form_type = 'rollershutter_quote'
    elif 'service_type' in data:
        form_type = 'service_request'
    elif 'appointment_date' in data:
        form_type = 'appointment'
    elif 'Address' in data:
        form_type = 'order'
    else:
        form_type = 'contact_form'
    
    name = data.get('name') or data.get('full_name') or 'Unknown'
    
    subjects = {
        'rollershutter_quote': f"🎯 New Rollershutter Quote from {name}",
        'service_request': f"🔧 New Service Request from {name}",
        'appointment': f"📅 New Appointment Request from {name}",
        'contact_form': f"📧 New Contact Form Submission from {name}",
        'order': f"💵 New Order Form Submission from {name}"
    }
    
    return {
        'type': form_type,
        'subject': subjects.get(form_type, f"📄 New Form Submission from {name}")
    }

def format_field_name(field_name):
    """Форматирует имя поля для отображения"""
    replacements = {
        '_': ' ',
        '-': ' ',
        'full_name': 'Full Name',
        'first_name': 'First Name',
        'last_name': 'Last Name',
        'phone': 'Phone Number',
        'telephone': 'Telephone',
        'mobile': 'Mobile Phone',
        'whatsapp': 'WhatsApp',
        'telegram': 'Telegram',
        'form_type': 'Form Type',
        'installation_location': 'Installation Location',
        'control_system': 'Control System',
        'installation_timeline': 'Installation Timeline'
    }
    
    field_name = replacements.get(field_name, field_name)
    return field_name.replace('_', ' ').title()

def format_field_value(value):
    """Форматирует значение поля"""
    if isinstance(value, list):
        return ', '.join(str(v) for v in value)
    elif isinstance(value, bool):
        return 'Yes' if value else 'No'
    else:
        return str(value)

def group_fields(data):
    """Группирует поля по категориям"""
    groups = {
        '👤 Contact Information': {},
        '📋 Form Details': {},
        '💼 Additional Information': {}
    }
    
    contact_fields = ['name', 'full_name', 'first_name', 'last_name', 'email', 
                     'phone', 'telephone', 'mobile', 'whatsapp', 'telegram', 
                     'company', 'position']
    
    form_fields = ['form_type', 'message', 'comments', 'description', 'budget', 
                  'timeframe', 'urgency', 'service_type', 'installation_location', 
                  'width', 'height', 'control_system', 'material']
    
    for key, value in data.items():
        display_name = format_field_name(key)
        
        if key in contact_fields:
            groups['👤 Contact Information'][display_name] = value
        elif key in form_fields:
            groups['📋 Form Details'][display_name] = value
        else:
            groups['💼 Additional Information'][display_name] = value
    
    # Удаляем пустые группы
    return {k: v for k, v in groups.items() if v}

def generate_email_html(data, form_info):
    """Генерирует HTML содержимое письма"""
    field_groups = group_fields(data)
    
    html_content = f"""
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Form Submission</title>
        <style>
            body {{ 
                font-family: Arial, sans-serif; 
                line-height: 1.6; 
                color: #333; 
                max-width: 600px; 
                margin: 0 auto; 
                padding: 20px;
                background: #f5f5f5;
            }}
            .container {{
                background: white;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }}
            .header {{ 
                background: #4CAF50; 
                color: white; 
                padding: 30px 20px; 
                text-align: center; 
            }}
            .header h1 {{
                margin: 0;
                font-size: 24px;
            }}
            .content {{ 
                padding: 30px; 
            }}
            .section {{
                margin-bottom: 25px;
            }}
            .section h2 {{
                color: #4CAF50;
                border-bottom: 2px solid #4CAF50;
                padding-bottom: 8px;
                margin-bottom: 15px;
            }}
            .field {{ 
                margin-bottom: 12px; 
                padding: 12px; 
                background: #f9f9f9; 
                border-left: 4px solid #4CAF50; 
                border-radius: 4px;
            }}
            .field-label {{ 
                font-weight: bold; 
                color: #555; 
                margin-bottom: 5px; 
                font-size: 14px;
            }}
            .field-value {{ 
                color: #333; 
                font-size: 16px;
                word-break: break-word;
            }}
            .footer {{ 
                text-align: center; 
                margin-top: 30px; 
                padding: 20px; 
                color: #666; 
                font-size: 12px;
                background: #f9f9f9;
                border-top: 1px solid #eee;
            }}
            .timestamp {{
                background: #e8f5e8;
                padding: 10px;
                border-radius: 5px;
                text-align: center;
                margin-bottom: 20px;
            }}
            .form-type {{
                background: #2196F3;
                color: white;
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 12px;
                display: inline-block;
                margin-bottom: 10px;
            }}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>{form_info['subject']}</h1>
                <p>New submission from your website</p>
            </div>
            
            <div class="content">
                <div class="timestamp">
                    <strong>Submitted:</strong> {datetime.now().strftime('%B %d, %Y at %I:%M %p')}
                    <div class="form-type">{form_info['type'].replace('_', ' ').title()}</div>
                </div>
    """
    
    for group_name, fields in field_groups.items():
        html_content += f"""
                <div class="section">
                    <h2>{group_name}</h2>
        """
        
        for field_name, field_value in fields.items():
            display_value = format_field_value(field_value)
            html_content += f"""
                    <div class="field">
                        <div class="field-label">{field_name}:</div>
                        <div class="field-value">{display_value}</div>
                    </div>
            """
        
        html_content += """
                </div>
        """
    
    html_content += f"""
            </div>
            
            <div class="footer">
                <p>📧 This email was automatically generated from your website form.</p>
                <p>🕒 Submission time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}</p>
            </div>
        </div>
    </body>
    </html>
    """
    
    return html_content

def generate_text_content(data, form_info):
    """Генерирует текстовую версию письма"""
    field_groups = group_fields(data)
    
    text_content = f"{form_info['subject'].upper()}\n"
    text_content += "=" * 50 + "\n\n"
    
    for group_name, fields in field_groups.items():
        text_content += f"{group_name}\n"
        text_content += "-" * len(group_name) + "\n"
        
        for field_name, field_value in fields.items():
            display_value = format_field_value(field_value)
            text_content += f"{field_name}: {display_value}\n"
        
        text_content += "\n"
    
    text_content += f"Submitted: {datetime.now().strftime('%B %d, %Y at %I:%M %p')}\n"
    text_content += f"Form Type: {form_info['type']}\n"
    
    return text_content

def send_email_smtp(data):
    """Отправляет email через SMTP"""
    form_info = get_form_type_and_subject(data)
    
    try:
        # Создаем сообщение
        msg = MIMEMultipart('alternative')
        msg['Subject'] = form_info['subject']
        msg['From'] = f"{SMTP_CONFIG['from_name']} <{SMTP_CONFIG['from_email']}>"
        msg['To'] = f"{SMTP_CONFIG['to_name']} <{SMTP_CONFIG['to_email']}>"
        msg['Reply-To'] = data.get('email', SMTP_CONFIG['from_email'])
        
        # Добавляем содержимое
        html_content = generate_email_html(data, form_info)
        text_content = generate_text_content(data, form_info)
        
        msg.attach(MIMEText(text_content, 'plain'))
        msg.attach(MIMEText(html_content, 'html'))
        
        # Подключаемся к SMTP серверу и отправляем
        with smtplib.SMTP(SMTP_CONFIG['host'], SMTP_CONFIG['port']) as server:
            server.starttls()
            server.login(SMTP_CONFIG['username'], SMTP_CONFIG['password'])
            server.send_message(msg)
        
        logger.info(f"Email sent successfully for form type: {form_info['type']}")
        return {'success': True, 'message': 'Email sent successfully', 'form_type': form_info['type']}
        
    except Exception as e:
        logger.error(f"SMTP error: {str(e)}")
        return {'success': False, 'message': f'SMTP error: {str(e)}'}

def save_to_file(data):
    """Сохраняет данные в файл (fallback)"""
    try:
        form_info = get_form_type_and_subject(data)
        filename = f"mail/quotes/{form_info['type']}_{datetime.now().strftime('%Y-%m-%d_%H-%M-%S')}_{os.urandom(4).hex()}.json"
        
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(data, f, ensure_ascii=False, indent=2)
        
        logger.info(f"Data saved to file: {filename}")
        return {'success': True, 'message': f'Data saved to file: {filename}', 'filename': filename}
        
    except Exception as e:
        logger.error(f"File save error: {str(e)}")
        return {'success': False, 'message': f'Failed to save data to file: {str(e)}'}

@app.route('/mail/api', methods=['POST', 'OPTIONS'])
def handle_form():
    """Обрабатывает отправку форм"""
    if request.method == 'OPTIONS':
        return '', 200
    
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({'success': False, 'message': 'No data received'}), 400
        
        logger.info(f"Received form submission: {data}")
        
        # Пытаемся отправить email
        email_result = send_email_smtp(data)
        
        if email_result['success']:
            return jsonify(email_result)
        else:
            # Если отправка не удалась, сохраняем в файл
            file_result = save_to_file(data)
            
            if file_result['success']:
                return jsonify({
                    'success': True,
                    'message': 'Email failed, but data saved locally',
                    'saved_to_file': True,
                    'form_type': get_form_type_and_subject(data)['type']
                })
            else:
                return jsonify({
                    'success': False,
                    'message': 'Both email and file save failed'
                }), 500
                
    except Exception as e:
        logger.error(f"API error: {str(e)}")
        return jsonify({'success': False, 'message': f'Server error: {str(e)}'}), 500

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({'status': 'healthy', 'timestamp': datetime.now().isoformat()})

if __name__ == '__main__':
    # Создаем необходимые директории
    os.makedirs('mail/quotes', exist_ok=True)
    
    # Запускаем приложение
    app.run(host='0.0.0.0', port=8900, debug=False)