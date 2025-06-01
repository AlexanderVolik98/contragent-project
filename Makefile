.PHONY: up migrate install build npm-install start

PHP_CONTAINER=php-fpm
NODE_CONTAINER=node
COMPOSER_CMD=composer install --no-interaction --prefer-dist
MIGRATE_CMD=php bin/console doctrine:migrations:migrate --no-interaction
NPM_INSTALL_CMD=npm install
NPM_BUILD_CMD=npm run build

up:
	docker compose up -d

install:
	docker compose exec $(PHP_CONTAINER) $(COMPOSER_CMD)

migrate:
	docker compose exec $(PHP_CONTAINER) $(MIGRATE_CMD)

npm-install:
	docker compose exec $(NODE_CONTAINER) $(NPM_INSTALL_CMD)

build:
	docker compose exec $(NODE_CONTAINER) $(NPM_BUILD_CMD)

start: up install migrate npm-install build
