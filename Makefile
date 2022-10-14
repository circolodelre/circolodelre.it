
serve:
	php -S 0.0.0.0

build: serve
	curl -a

install:
	composer install