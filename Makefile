

serve:
	php -S localhost:8080 -t ./src ./src/router.php

build:
	@docker compose run --rm php -f src/tasks/build.php

install:
	composer install

update:
	composer update

start:
	@php -S localhost:8080

push:
	git add .
	git commit -am fix
	git push

release: push

