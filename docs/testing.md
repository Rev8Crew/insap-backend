## Настройка тестов

### Создаем и настраиваем базу данных для тестов
Для начала подключимся к БД через шторм, для этого:
- Выбираем database вкладку слева, new -> mysql
- Вводим user\password из .env
- Сохраняем
- Тоже самое и для MongoDB

Создаем новую базу данных db_mysql_00000000_insap_test через шторм(Database->right click->new->schema)
Создаем новую базу данных db_mongodb_00000000_insap_test через http://localhost:8001

Выполняем в консоли в новой бд MySQL (Database->Jump to Query Console->new)
```GRANT ALL PRIVILEGES ON db_mysql_00000000_insap_test.* TO 'user';```
### Make

Выполняем:
`make refresh`
`make test`

### Дополнение
Тесты запускаются с env testing, если нужно что-то поменять, то используем .env.testing
Helper/EnvHelper::isLocalTestEnvironment проверяет, является ли текущее окружение локальным\тестовым
