.PHONY: up migrate install

PHP_CONTAINER=php-fpm
COMPOSER_CMD=composer install --no-interaction --prefer-dist
MIGRATE_CMD=php bin/console doctrine:migrations:migrate --no-interaction

up:
	docker compose up -d

install:
	docker compose exec $(PHP_CONTAINER) $(COMPOSER_CMD)

migrate:
	docker compose exec $(PHP_CONTAINER) $(MIGRATE_CMD)

start: up install migrate
