# Тестове Завдання: Інтеграція CRM API (AddLead & GetStatuses)

Це рішення реалізує інтеграцію з наданим CRM API, використовуючи чистий PHP та Docker для локального розгортання.

## 🔗 Здані Результати

1. **Git Репозиторій:** [https://github.com/gybich-oleg/teast_lead.git]

## 🛠️ Вимоги до Середовища

Для локального запуску проєкту необхідні:

* Docker та Docker Compose
* Git

## ⚙️ Інструкція з Локального Запуску

1.  **Клонуйте репозиторій:**
    ```bash
    git clone <https://github.com/gybich-oleg/teast_lead.git>
    cd <test_lead>
    ```

2.  **Запустіть контейнери:**
    У кореневій папці виконайте:
    ```bash
    docker-compose up -d --build
    ```
    (Ця команда збере образ PHP з підтримкою cURL та запустить Nginx.)

3.  **Перевірка:**
    Відкрийте ваш браузер та перейдіть за адресою: `http://localhost`

## 📂 Структура Проєкту

* `src/index.php`: Сторінка з формою для `AddLead`.
* `src/statuses.php`: Сторінка з таблицею для `GetStatuses` та фільтром по даті.
* `Dockerfile`: Конфігурація образу PHP (з cURL).
* `docker-compose.yml`: Налаштування Nginx та PHP-FPM.

## 📝 Зауваження щодо API

* **API URL:** Використовується `https://crm.belmar.pro/api/v1/`.
* **Токен:** `ba67df6a-a17c-476f-8e95-bcdb75ed3958`.
* **Статичні поля:** `box_id=28`, `offer_id=5`, `countryCode=GB`, `language=en`, `password=qwerty12` передаються статично у фоновому режимі.
* **Вирішення проблеми з "Failed to register a lead":** Для успішного додавання ліда необхідно використовувати **унікальні та валідні дані**, а телефонний номер повинен відповідати коду країни `GB` (наприклад, починатися з `+44`).