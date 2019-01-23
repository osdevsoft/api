#Osds API
Basic API system to handle basic CRUD operations on a Laravel - Mysql Server

##Install on Symfony

> composer require osds/api

> ./vendor/osds/api/bin/post-install.sh

> Configurar .env

> una vez creada la DB, para crear las entidades de Symfony en src/Entity, ejecutar:
```
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity
```
cambiar los atriutos de las entidades a underscore y generar getters y setters 




pending
-------
hacer que el annotation genere tambien los one to many