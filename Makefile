# Выполним по умолчанию, при запуске пустого make
.DEFAULT_GOAL := help

# Подключим файл конфигурации
include app/.env

# И укажем его для docker-compose
ENV = --env-file app/.env

# Добавим красоты и чтоб наши команды было видно в теле скрипта
PURPLE = \033[1;35m $(shell date +"%H:%M:%S") --
RESET = --\033[0m

# Считываем файл, всё что содержит двойную решётку # Это описание к командам
help:
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-17s\033[0m %s\n", $$1, $$2}'
.PHONY: help

# Если это developer окружение, то подключим debug профиль
PROFILE =
ifeq ($(ENVIRONMENT),developer)
	PROFILE := -f docker-compose-blog.yml
else
	PROFILE :=
endif

init: ## Инициализация проекта
init: docker-down docker-pull docker-build docker-up

update: ## Пересобрать контейнер, обновить композер и миграции
update: docker-down docker-pull docker-build docker-up

restart: ## Restart docker containers
restart: docker-down docker-up

docker-up: ## Поднимем контейнеры
	@echo "$(PURPLE) Поднимем контейнеры $(RESET)"
	docker-compose $(ENV) $(PROFILE) up -d

docker-build: ## Соберём образы
	@echo "$(PURPLE) Соберём образы $(RESET)"
	docker-compose $(ENV) $(PROFILE) build

docker-pull: ## Поучим все контейнеры
	@echo "$(PURPLE) Поучим все контейнеры $(RESET)"
	docker-compose $(ENV) $(PROFILE) pull --include-deps

docker-down: ## Остановим контейнеры
	@echo "$(PURPLE) Остановим контейнеры $(RESET)"
	docker-compose $(ENV) $(PROFILE) down --remove-orphans