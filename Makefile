
## ======
## Vendor
## ======

install:
	@docker compose run --rm php composer install

update:
	@docker compose run --rm php composer update

dump-autoload:
	@docker compose run --rm php composer dump-autoload

## =====
## Tasks
## =====

build:
	@docker compose run --rm php -f src/tasks/build.php

download-events:
	@docker compose run --rm php -f src/tasks/download-events.php

sync: download-events

## ====
## Misc
## ====

serve:
	@docker compose build php
	@docker compose run --rm -p 8080:8080 php -S 0.0.0.0:8080 src/router.php

start: serve

push:
	@git add .
	@git commit -am fix || true
	@git push

release: push

## ======
## Docker
## ======

docker-build:
	docker compose build

## =====
## Tests
## =====

test-docs: build
	@docker compose run --rm -p 8080:8080 php -S 0.0.0.0:8080 -t docs
