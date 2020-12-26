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
m1:
	docker-compose run --rm php php bin/console make:migration
m2:
	docker-compose run --rm php php bin/console doctrine:migrations:migrate
fixture:
	docker-compose run --rm php php bin/console doctrine:fixtures:load
entity:
	docker-compose run --rm php php bin/console make:entity
