Install on Symfony
-------------------

composer require osds/api

copy content of src/Framework/Symfony/config/.env.example to /.env

ejecutar ./vendor/osds/api/bin/post-install.sh

una vez creada la DB, para crear las entidades de Symfony en src/Entity, ejecutar:
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity
cambiar los atriutos de las entidades a underscore y generar getters y setters 

---
Basic API system to handle basic CRUD operations on a Laravel - Mysql Server


pending
-------
hacer que el annotation genere tambien los one to many