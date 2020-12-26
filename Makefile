up:
	docker-compose up -d
upp:
	docker-compose up --build -d
down:
	docker-compose down
logs:
	docker-compose logs
cc:
	docker-compose run --rm php php bin/console cache:clear
comi:
	docker-compose run --rm php composer install
comu:
	docker-compose run --rm php composer update
install:
	docker-compose run --rm php php bin/console doctrine:database:create && docker-compose run --rm php php bin/console doctrine:migrations:migrate && docker-compose run --rm php php bin/console doctrine:fixtures:load
fixture:
	docker-compose run --rm php php bin/console doctrine:fixtures:load
