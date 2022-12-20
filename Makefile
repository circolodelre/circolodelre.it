
## ======
## Vendor
## ======

install:
	composer install

update:
	composer update

## =====
## Tasks
## =====

build:
	@docker compose run --rm php -f src/tasks/build.php

## ====
## Misc
## ====

serve:
	php -S localhost:8080 -t ./src ./src/router.php

start:
	@php -S localhost:8080

push:
	git add .
	git commit -am fix
	git push

release: push
