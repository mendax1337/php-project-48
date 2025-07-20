install:
	composer install
validate:
	composer validate
update:
	composer update
test:
	composer exec --verbose phpunit tests
lint:
	vendor/bin/phpcs --standard=PSR12 src bin tests
	vendor/bin/phpstan analyse -c phpstan.neon --ansi
fix:
	composer exec -v phpcbf -- --standard=PSR12 --colors src bin tests
