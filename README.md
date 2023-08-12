Запуск сервера: 
php -S localhost:8000 -t public
C:\OSPanel\modules\php\PHP_8.0\php.exe -S localhost:8000 -t public

история:

1. За основу взял проект "jwt_laravel_vue_back"
- удалил гит-репо:
  Remove-Item -Recurse -Force .git
- новый Git репозиторий:
  git init
git add .
git commit -m "За основу взял проект jwt_laravel_vue_back"

2. Установил зависимости:
- composer install

*) скопировал .env, без него внутренний сервер работать не будет

*) Сгенерировать новый секретный ключ:
php artisan jwt:secret 

*) Создал базу данных "social_network_1", подключился, накатил миграции, получил данные из таблицы

*) Протестировал авторизацию через api
