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

To start the project use `docker-compose up`. API will be accessible at <http://localhost:8000/api/>.

You can either run `npm run dev` in `frontend` dir and get the UI at <http://loclahost:8080/>, or navigate
to <http://localhost:8000/> directly.

Creating a DB
=============

To create the database first run the whole stack with

`docker-compose up`

and then do

`docker-compose run --rm --entrypoint php -v $(pwd)/backend/init_db.php:/init_db.php php /init_db.php`

A user with login "`maks`" and password "`46EpOt`" is created by this script as well.

Populating DB with fake data
----------------------------

Fake data is created with python script `gen_db.py`. IP, port and password of MySQL server is hardcoded in
the script.

API
===

API is somewhat REST-like. All endpoints are at `/api/`. API methods accept data and return results in
JSON object.

Currently implemented API methods are:

- `POST` `/api/good/new` - creates a new good from JSON description and returns its ID
- `GET` `/api/good/{id}` - returns good description by its id
- `PUT` `/api/good/{id}` - update good by specifying all of its fields
- `DELETE` `/api/good/{id}` - delete a good from the DB
- `GET` `/api/goods/{byid|byprice}?count=20&offset=0` - get a list of top goods by id or by price (ascending) with given offset and count
- `POST` `/api/login` - obtain new auth token by username & password

Price is stored as `int` value to avoid problems with floats. In a real e-shop this would be treated as some
fixed-point rational number, e.g. displayed divided by 100, meaning two fixed digits after the dot.

Auth
----

All API methods except `/api/login` expect `Authorization: Token ...` header with a valid token. Token
can be obtained by `/api/login` methods by supplying username and password in JSON object.
Token is generated with `uuid_create()` function and stored in memcached.

Caching
=======

Only the sorted list of all goods is cached, by id and by price. Each page (by 20 items) is cached separately.
Top 10 pages of each list are cached. Additionally we store max id and value in each list.
When a good is added, modified or deleted, it's id and price are checked against those values. If modified
item lies in cached list, entire cache is invalidated and regenerated on next access.

Frontend
========

Frontend is implemented with little effort for style or idiomatic code using Vue.js framework. Four pages are
present without any navigation in browser:

- Login page
- Goods list with sorting and pagination
- Good detailed view
- Good creation and editing page

Switching of the pages is done by clicking links on them.

