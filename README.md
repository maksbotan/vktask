A test task for VK job interview

Task
====

Реализовать простую систему просмотра списка товаров.

Товар описывается несколькими полями: id, название, описание, цена, url картинки.

Требуется:
- интерфейс создания/редактирования/удаления товара
- страница просмотра списка товаров

Товары можно просмотривать отсортированные по цене или по id.

Поддерживать количество товаров в списке – до 1000000.
Устойчивость к нагрузке – 1000 запросов к списку товаров в минуту.
Время открытия страницы списка товаров < 500 мс.

Техника:
- PHP (без ООП), mysql, memcached.
- Фронтэнд - на ваше усмотрение.
- Проект должен быть на гитхабе и отражать процесс разработки.

В результате — ссылка на гитхаб и развёрнутое демо.

Project structure
=================

The project has completely separate frontend single-page application and backend API server.
Backend is stored in `backend/` folder. Backend uses single entry point for all api methods:
`backend\src\index.php`. URI of request is passed fron nginx via `PATH_INFO` FastCGI variable.

`index.php` implements a simple router, matching URI against a set of predefined patterns and then
`include`-ing a PHP file with the implementation of particular API endpoint. Router opens MySQL connection
as well.

The project can be run in Docker with `docker-compose`. It uses 4 containers: `php-fpm` backend server,
MySQL server, memcached server and nginx web server, which serves frontend static content and forwards
API requests to php.

To start the project use `docker-compose up`. API will be accessible at <http://localhost:8080/api/>.

Creating a DB
=============

To create the database first run the whole stack with

`docker-compose up`

and then do

`docker-compose run  --entrypoint php -v $(pwd)/backend/init_db.php:/init_db.php php /init_db.php`

API
===

API is somewhat REST-like. All endpoints are at `/api/`. API methods accept data and return results in
JSON object.

Currently implemented API methods are:

- `POST` `/api/good/new` - creates a new good from JSON description and returns its ID
- `GET` `/api/good/{id}` - returns good description by its id

