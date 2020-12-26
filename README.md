# redsms

# install

* docker-compose up -d
* docker-compose run --rm php composer install
* docker-compose run --rm php php bin/console doctrine:migrations:migrate
* docker-compose run --rm php php bin/console doctrine:fixtures:load

Далее зайти на http://localhost/
