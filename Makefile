.PHONY: sphinx-config up install migrate npm-install build start

PHP_CONTAINER   = php-fpm
NODE_CONTAINER  = node
COMPOSER_CMD    = composer install --no-interaction --prefer-dist
MIGRATE_CMD     = php bin/console doctrine:migrations:migrate --no-interaction
NPM_INSTALL_CMD = npm install
NPM_BUILD_CMD   = npm run build

SPHINX_TEMPLATE = docker/sphinx/sphinx.conf.template
SPHINX_CONFIG   = docker/sphinx/sphinx.conf

sphinx-config:
	@echo ">>> Генерируем sphinx.conf из шаблона…"
	# Экспортим переменные из .env.docker, затем подставляем в шаблон
	set -a && . ./.env.docker && set +a && \
	envsubst '$$POSTGRES_USER $$POSTGRES_PASSWORD $$POSTGRES_DB' \
	  < $(SPHINX_TEMPLATE) > $(SPHINX_CONFIG)

up: sphinx-config
	docker compose --env-file .env.docker up -d

install:
	docker compose exec $(PHP_CONTAINER) $(COMPOSER_CMD)

migrate:
	docker compose exec $(PHP_CONTAINER) $(MIGRATE_CMD)

npm-install:
	docker compose exec $(NODE_CONTAINER) $(NPM_INSTALL_CMD)

build:
	docker compose exec $(NODE_CONTAINER) $(NPM_BUILD_CMD)

start: up install migrate npm-install build
	@echo ">>> Все сервисы запущены, зависимости установлены, миграции прогнаны, фронтенд собран."
