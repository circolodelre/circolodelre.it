
## ======
## Vendor
## ======

install:
	@docker compose run --rm php composer install

update:
	@docker compose run --rm php composer update

## =====
## Tasks
## =====

build:
	@docker compose run --rm php -f src/tasks/build.php

## ====
## Misc
## ====

serve:
	@docker compose run --rm -p 8080:8080 php -S 0.0.0.0:8080 src/router.php

start:
	@php -S localhost:8080

push:
	git add .
	git commit -am fix
	git push

release: push

## ======
## Docker
## ======

docker-build:
	docker compose build