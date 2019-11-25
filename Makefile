.PHONY: test
test: phpstan phan phpunit

phpunit:
	vendor/bin/phpunit

phpstan:
	vendor/bin/phpstan analyse --level 7 src

phan:
	vendor/bin/phan
