Учебный API проект Развернуть проект можно следующим способом:
- клонировать с гитхаба: git clone https://github.com/FursAndrey/shop_v5.git
- загрузить Laravel: composer install
- копировать .env.example в .env: copy .env.example .env
- создать новый ключ для проекта команда: php artisan key:generate
- создаю базу данных для проекта
- в файле .env настроить подключение к базу данных
- создать таблицы: php artisan migrate
- запустить используемый сервер (в моем случае это OpenServer)