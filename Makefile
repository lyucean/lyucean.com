# Выполним по умолчанию, при запуске пустого make
.DEFAULT_GOAL := help

# Подключим файл конфигурации
include app/.env

# И укажем его для docker compose
ENV = --env-file app/.env

# Дата время
BACKUP_DATETIME := $(shell date '+%Y-%m-%d')

# Добавим красоты и чтоб наши команды было видно в теле скрипта
PURPLE = \033[1;35m $(shell date +"%H:%M:%S") --
RESET = --\033[0m

# Считываем файл, всё что содержит двойную решётку # Это описание к командам
help:
	@grep -E '^[a-zA-Z-]+:.*?## .*$$' Makefile | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-17s\033[0m %s\n", $$1, $$2}'
.PHONY: help

# Если это developer окружение, то подключим debug профиль
PROFILE =
ifeq ($(ENVIRONMENT),development)
	PROFILE := --profile dev
else
	PROFILE := --profile prod
endif

update: ## Пересобрать контейнер, обновить композер и миграции
update: docker-down docker-pull docker-build docker-up

restart: ## Restart docker containers
restart: docker-down docker-up

docker-up: ## Поднимем все контейнеры
	@echo "$(PURPLE) Поднимем все контейнеры $(RESET)"
	@docker network inspect web >/dev/null 2>&1 || docker network create web
	docker compose $(ENV) $(PROFILE) up -d

docker-build: ## Соберём все контейнеры
	@echo "$(PURPLE) Соберём образы $(RESET)"
	docker compose $(ENV) $(PROFILE) build

docker-pull:
	@echo "$(PURPLE) Поучим все контейнеры $(RESET)"
	docker compose $(ENV) $(PROFILE) pull --include-deps

docker-down: ## Остановим контейнеры
	@echo "$(PURPLE) Остановим контейнеры $(RESET)"
	docker compose $(ENV) $(PROFILE) down --remove-orphans

# Команды для работы с дампами на продакшене ----------------------------------------------------------------------------
backup-db:  ## Снимем дамп с БД
	@echo "$(PURPLE) Снимем дамп с БД $(RESET)"
	@docker compose $(ENV) exec mysql sh -c 'exec mysqldump -u root -p"${MYSQL_ROOT_PASSWORD}" --default-character-set=utf8mb4 --single-transaction --quick --routines --max-allowed-packet=512M --skip-extended-insert "${WORDPRESS_DB_NAME}"' > "${BACKUPS_FOLDER}/$(BACKUP_DATETIME)_LS.sql"

backup-file:  ## Снимем дамп файлов с папки wordpress
	@echo "$(PURPLE) Создадим архив файлов $(RESET)"
	tar --warning=no-file-changed -czf ${BACKUPS_FOLDER}/${BACKUP_DATETIME}_LS.file.gz \
		--exclude='app/wordpress/wp-content/cache' \
		--exclude='app/wordpress/wp-content/backup' \
		--exclude='app/wordpress/wp-content/upgrade-temp-backup' \
		--exclude='app/wordpress/wp-content/upgrade/' \
		--exclude='app/wordpress/wp-content/plugins/*/cache/' \
		--exclude='app/wordpress/wp-content/backups' -C ./app/wordpress .

import-backup:  ## Импорт БД из сегодняшнего дампа (удобно восстанавливать, если что-то сломал в настройках)
	@echo "$(PURPLE) Импорт БД из дампа $(RESET)"
	@if [ -f "${BACKUPS_FOLDER}/$(BACKUP_DATETIME)_LS.sql" ]; then \
		docker compose $(ENV) exec -T mysql sh -c 'exec mysql --default-character-set=utf8mb4 --max-allowed-packet=1G --binary-mode -u root -p"$(MYSQL_ROOT_PASSWORD)" "$(WORDPRESS_DB_NAME)"' < "${BACKUPS_FOLDER}/$(BACKUP_DATETIME)_LS.sql"; \
	else \
		echo "Дампа за сегодня нет!"; \
	fi

# Команды для инициализации проекта на локальной машине -----------------------------------------------------------------
init: ## Инициализация проекта для локальной разработки
ifeq ($(ENVIRONMENT), development)
	@echo "Запуск команды init в окружении $(ENVIRONMENT)"
	@$(MAKE) docker-down
	@$(MAKE) docker-pull
	@$(MAKE) docker-build
	@$(MAKE) docker-up-mysql
	@$(MAKE) update-backup
	@$(MAKE) update-dump
	@$(MAKE) update-urls
	@$(MAKE) update
	@$(MAKE) open-url
else
	@echo "Команда init может быть запущена только в окружении development. Текущее окружение: $(ENVIRONMENT)"
endif

sync: ## Быстрое обновление: БД и файлы с прода (без пересборки контейнеров)
ifeq ($(ENVIRONMENT), development)
	@echo "$(PURPLE) Быстрое обновление проекта с прода $(RESET)"
	@$(MAKE) docker-up-mysql
	@$(MAKE) fresh-backup
	@$(MAKE) update-backup
	@$(MAKE) update-dump
	@$(MAKE) update-urls
	@echo "$(PURPLE) Обновление завершено! $(RESET)"
else
	@echo "Команда sync может быть запущена только в окружении development. Текущее окружение: $(ENVIRONMENT)"
endif

sync-db: ## Только БД с прода: дамп на сервере + rsync SQL, без архива файлов и без распаковки wp-content
ifeq ($(ENVIRONMENT), development)
	@echo "$(PURPLE) Обновление только БД с прода $(RESET)"
	@$(MAKE) docker-up-mysql
	@$(MAKE) fetch-db-backup
	@$(MAKE) update-dump
	@$(MAKE) update-urls
	@echo "$(PURPLE) БД обновлена $(RESET)"
else
	@echo "Команда sync-db может быть запущена только в окружении development. Текущее окружение: $(ENVIRONMENT)"
endif

fresh-backup:
	@if [ ! -f "./backup/$(BACKUP_DATETIME)_LS.sql" ] || [ ! -f "./backup/$(BACKUP_DATETIME)_LS.file.gz" ]; then \
		echo "$(PURPLE) Запуск команды 'make backup-db и backup-file' на удаленном сервере $(RESET)"; \
		sshpass -p"${SSH_PASSWORD}" ssh ${SSH_USER}@${SSH_HOST} "cd ${SSH_FOLDER} && make backup-db && make backup-file"; \
	else \
		echo "$(PURPLE) Все необходимые файлы уже существуют локально, пропускаем скачивание бэкапа $(RESET)"; \
	fi

# Только SQL: снимает дамп на проде и кладёт в ./backup (архив wp-content не трогаем)
fetch-db-backup: docker-up-mysql
	@mkdir -p backup
	@echo "$(PURPLE) Снимаем дамп на сервере и скачиваем SQL $(RESET)"
	sshpass -p"${SSH_PASSWORD}" ssh ${SSH_USER}@${SSH_HOST} "cd ${SSH_FOLDER} && make backup-db"
	sshpass -p"${SSH_PASSWORD}" rsync -avP -e "ssh -o StrictHostKeyChecking=no" ${SSH_USER}@${SSH_HOST}:${BACKUPS_FOLDER}/$(BACKUP_DATETIME)_LS.sql ./backup/

fetch-backup: fresh-backup docker-up-mysql
	@mkdir -p backup
	@if [ ! -f "./backup/$(BACKUP_DATETIME)_LS.sql" ]; then \
		echo "$(PURPLE) Скачиваем дамп с удаленного сервера $(RESET)"; \
		sshpass -p"${SSH_PASSWORD}" rsync -avP -e "ssh -o StrictHostKeyChecking=no" ${SSH_USER}@${SSH_HOST}:${BACKUPS_FOLDER}/$(BACKUP_DATETIME)_LS.sql ./backup; \
	else \
		echo "$(PURPLE) Дамп БД уже существует локально, пропускаем скачивание $(RESET)"; \
	fi
	@if [ ! -f "./backup/$(BACKUP_DATETIME)_LS.file.gz" ]; then \
		echo "$(PURPLE) Скачиваем архив с удаленного сервера $(RESET)"; \
		sshpass -p"${SSH_PASSWORD}" rsync -avP -e "ssh -o StrictHostKeyChecking=no" ${SSH_USER}@${SSH_HOST}:${BACKUPS_FOLDER}/${BACKUP_DATETIME}_LS.file.gz ./backup; \
	else \
		echo "$(PURPLE) Архив файлов уже существует локально, пропускаем скачивание $(RESET)"; \
	fi

update-backup: fetch-backup # Распакуем архив на локальной машине
	@echo "$(PURPLE) Удаляем все лишние файлы в папке перед распаковкой $(RESET)"
	rm -rf ./app/wordpress/*
	@echo "$(PURPLE) Распаковываем архив на локальной машине $(RESET)"
	tar -xzf ./backup/${BACKUP_DATETIME}_LS.file.gz -C ./app/wordpress

docker-up-mysql: ## Поднимем базу данных для разработки
	@echo "$(PURPLE) Поднимем базу данных $(RESET)"
	docker compose $(ENV) $(PROFILE) up -d mysql_dev

# Путь к дампу: DUMP_SQL=./backup/foo.sql или по умолчанию сегодняшний, иначе последний *_LS.sql
update-dump:  ## Импорт БД из дампа
	@echo "$(PURPLE) Импорт БД из дампа $(RESET)"
	@DUMP_FILE="$(DUMP_SQL)"; \
	[ -z "$$DUMP_FILE" ] && DUMP_FILE="$$DUMP_SQL"; \
	if [ -z "$$DUMP_FILE" ] || [ ! -f "$$DUMP_FILE" ]; then DUMP_FILE="./backup/$(BACKUP_DATETIME)_LS.sql"; fi; \
	if [ ! -f "$$DUMP_FILE" ]; then DUMP_FILE=$$(ls -t ./backup/*_LS.sql 2>/dev/null | head -1); fi; \
	if [ -z "$$DUMP_FILE" ] || [ ! -f "$$DUMP_FILE" ]; then echo "Нет файла дампа: положите backup/YYYY-MM-DD_LS.sql или задайте DUMP_SQL="; exit 1; fi; \
	echo "$(PURPLE) Файл: $$DUMP_FILE $(RESET)"; \
	docker compose $(ENV) exec -T mysql_dev sh -c 'exec mysql --default-character-set=utf8mb4 --max-allowed-packet=1G --binary-mode -u root -p"$(MYSQL_ROOT_PASSWORD)" "$(WORDPRESS_DB_NAME)"' < "$$DUMP_FILE"

update-urls:
	@echo "$(PURPLE) Обновление URL-адресов WordPress для окружения $(ENVIRONMENT)... $(RESET)"
	@docker compose $(ENV) exec -T mysql_dev sh -c "mysql -u root -p'$(MYSQL_ROOT_PASSWORD)' $(WORDPRESS_DB_NAME) -e \"UPDATE wp_options SET option_value = '$(LOCAL_URL)' WHERE option_name = 'siteurl';\""
	@docker compose $(ENV) exec -T mysql_dev sh -c "mysql -u root -p'$(MYSQL_ROOT_PASSWORD)' $(WORDPRESS_DB_NAME) -e \"UPDATE wp_options SET option_value = '$(LOCAL_URL)' WHERE option_name = 'home';\""

# Цель для открытия URL в браузере
open-url:
	@echo "Открытие URL $(LOCAL_URL)/ в браузере..."
ifeq ($(shell uname), Darwin)
	@open $(LOCAL_URL)/
else
ifeq ($(shell uname), Linux)
	@xdg-open $(LOCAL_URL)/
endif
endif

logs: ## Показать логи WordPress контейнера (последние 100 строк)
	@echo "$(PURPLE) Просмотр логов WordPress контейнера $(RESET)"
	docker compose $(ENV) $(PROFILE) logs --tail=300 -f wordpress
