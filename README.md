Как развернуть проект:
 
1. docker-compose up - приложение будет доступно по адресу localhost:8000  и docker-compose exec php composer install


2. сделать get запросы на ендпоинты /migrate и /seed (выполнятся миграции и заполнение базы стартовыми данными)


3. залогиниться и получить Bearer токен через едпоинт /api/v1/login (в дальнейшем токен будет использоваться для доступа к остальным ендпоинтам)
- пользователя с нужной ролью и другие сущности для работы можно посмотреть в базе данных через phpmyadmin, который доступен по адресу localhost:8080

- пароль для всех пользователей password123


Вся информация о ендпоинтах находится в файле openapi.yaml

Примнчание: при тестировании приложения ендпоинты нужно указывать без лишних / в конце

Описание:

В приложении реализована базовая логика оформления и получения заказа для каждой ролей пользователя.
Каждый пользователь видит только доступные ему заказы согласно его роли и id

Роли и права:

1. Покупатель: может оформлять заказ, рассчитать стоимость доставки (использован mock ответа), видеть список своих заказов, детальную информацию по ним


2. Продавец: может оформлять заказ, рассчитать стоимость доставки (использован mock ответа), видеть список своих заказов, детальную информацию по ним, также может передать заказ курьеру


4. Курьер: может видеть список своих заказов, детальную информацию по ним, рассчитать стоимость доставки (использован mock ответа)





