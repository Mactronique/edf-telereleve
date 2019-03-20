
dcconf=--file docker-compose.yml
tool=docker-compose $(dcconf) run --rm toolphp

.PHONY: console

console:
	$(tool) bash


composer.lock: composer.json composer.phar
	$(tool) bash -ci 'phpdismod -v ALL -s ALL xdebug && php composer.phar update --no-scripts --optimize-autoloader $(lib)'
	$(tool) bash -ci 'chown -R $(stat -c "%u" /sources):$(stat -c "%g" /sources) /sources'

vendor: composer.lock
	$(tool) bash -ci 'phpdismod -v ALL -s ALL xdebug && php composer.phar install --no-scripts --optimize-autoloader'
	$(tool) bash -ci 'chown -R $(stat -c "%u" /sources):$(stat -c "%g" /sources) /sources'

composer.phar:
	$(eval EXPECTED_SIGNATURE = "$(shell wget -q -O - https://composer.github.io/installer.sig)")
	$(eval ACTUAL_SIGNATURE = "$(shell php -r "copy('https://getcomposer.org/installer', 'composer-setup.php'); echo hash_file('SHA384', 'composer-setup.php');")")
	@if [ "$(EXPECTED_SIGNATURE)" != "$(ACTUAL_SIGNATURE)" ]; then echo "Invalid signature"; exit 1; fi
	php composer-setup.php
	rm composer-setup.php
