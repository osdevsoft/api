#Osds API
Basic API system to handle basic CRUD operations on a Laravel - Mysql Server

##Install on Symfony

> composer create-project symfony/skeleton api

> composer config repositories.repo-name vcs https://github.com/osdevsoft/api

> composer config minimum-stability dev

> composer require osds/api:master

> ./vendor/osds/api/bin/post-install.sh

> Configurar .env

> una vez creada la DB, para crear las entidades de Symfony en src/Entity, ejecutar:
```
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity


launch behaviour tests:
./vendor/bin/behat --config vendor/osds/api/tests/behaviour/bootstrap/behat.yml 


pending
-------
hacer que el annotation genere tambien los one to many