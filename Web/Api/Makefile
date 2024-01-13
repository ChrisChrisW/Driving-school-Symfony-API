build:
	$(MAKE) composer-install
	$(MAKE) database-create
	$(MAKE) fixtures

update:
	$(MAKE) composer-install
	$(MAKE) database-update
	$(MAKE) reload-fixtures
	$(MAKE) cache-clear

composer-install:
	composer install

database-create:
	$(MAKE) database-drop
	php bin/console doctrine:schema:create

database-drop:
	php bin/console doctrine:schema:drop --force

database-update:
	php bin/console doctrine:schema:update --dump-sql --force --complete

fixtures:
	php bin/console doctrine:fixtures:load --quiet

reload-fixtures:
	$(MAKE) database-update
	$(MAKE) fixtures

cache-clear:
	php bin/console cache:clear --no-warmup