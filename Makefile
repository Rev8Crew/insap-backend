.PHONY: build
build:
	docker-compose build

.PHONY: up
up:
	docker-compose -f docker-compose.yml up -d --remove-orphans

.PHONY: down
down:
	docker-compose -f docker-compose.yml down --remove-orphans

.PHONY: refresh
refresh:
	docker exec insap_web php artisan optimize:clear
	docker exec insap_web php artisan config:clear
	docker exec insap_web php artisan db:wipe --database mongodb_test
	docker exec insap_web php artisan migrate --database mongodb_test --path database/migrations/mongodb
	docker exec insap_web php artisan db:wipe --database mysql_test
	docker exec insap_web php artisan migrate --database mysql_test
	docker exec insap_web php artisan migrate:plugin adcp mysql_test
	docker exec insap_web php artisan db:seed --database mysql_test

.PHONY: test
test:
	docker exec insap_web php artisan test --do-not-cache-result -c phpunit.xml

.PHONY: swagger
swagger:
	docker exec insap_web php artisan l5-swagger:generate

