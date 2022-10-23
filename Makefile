
serve:
	php -S localhost:8080 -t ./src ./src/router.php

build: serve
	curl -a

install:
	composer install

start:
	@php -S localhost:8080
