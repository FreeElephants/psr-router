PATH := $(shell pwd)/bin:$(PATH)
$(shell cp -n dev.env .env)
include .env

install: build
	composer install
	cp -n phpunit.xml.dist phpunit.xml

build:
	docker build --build-arg PHP_VERSION=$(PHP_VERSION) -t $(PHP_DEV_IMAGE):$(REVISION) .

test:
	php vendor/bin/phpunit

cs:
	php vendor/bin/php-cs-fixer fix
