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

3. Создал 5 моделей + миграции к этим моделям
>php artisan make:Model Post -m
 
>php artisan make:Model PostImage -m

>php artisan make:Model LikedPost -m

>php artisan make:Model SubscriberFollowing -m

>php artisan make:Model Comment -m

+ заполнил созданные миграции, накатил

4. Выполнил:
> php artisan storage:link

- После выполнения команды php artisan storage:link,
все файлы и папки в storage/app/public становятся доступными через публичный URL,
используя префикс /storage.

--1) при выборе изображения, создаётся запись в базе, изображение загружается на сервер,
изображение отрисовывается перед публикацией поста
--2) при клике "публикация", "пост" записывается в базу, 
для записи изображения -> назначается post_id, "active" = true;
--3) изображения, которые были добавлены, но отменены -> удаляются из стораджа, и базы.

5. Создал роут, который возвращает посты аутентифицированного пользователя.

6. Создал роут, возвращает всех пользователей, кроме аутентифицированного

7. Создал роут, получение постов, по id юзера, через отношение моделей

8. Реализовал функционал подписки на другого пользователя
