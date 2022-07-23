# Установка проект

## Docker

- docker-compose -f docker-compose.yml up -d

## Настройка MongoDB

- docker exec -it insap_mongo bash
- Необходимо выполнить следующие команды:

```
mongo init
use admin
db.auth("root", "example")
use db_mongodb_00000000_insap;
db.createUser({
user: "user",
pwd: "example",
roles:[
    { "role": "clusterMonitor", "db": "admin" },
    "readWrite",
    "dbAdmin"
    ]
})
```

## Настройка Web

- docker exec -it insap-backend_insap-web_1 bash
- composer i
- php artisan migrate --path=/database/migrations/mongodb
- php artisan migrate --seed
- php artisan test (Для успешного выполнения всех тестов, необходимо наличие файлов с данными приборов)
