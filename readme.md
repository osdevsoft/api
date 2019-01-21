Install on Symfony
-------------------

composer require osds/api

add 
  "autoload": {
    "psr-4": {
      "App\\": "src/",
      "Osds\\Api\\": "vendor/osds/api/src"
    }
  }

to /composer.json

copy content of src/Framework/Symfony/config/.env.example to /.env

copy src/Framework/Symfony/config/osds_api.yaml to config/packages/osds_api.yaml

on config/routes.yaml, add
osds_api:
    resource: '../../vendor/osds/api/src/Framework/Symfony/'
    type:     annotation

una vez creada la DB, para crear las entidades de Symfony en src/Entity, ejecutar:
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity
cambiar los atriutos de las entidades a underscore y generar getters y setters 

doc:
no se puede utlizar el findby porque no permite LIKE

---

Laravel

add to .env (on project requesting the API):
API_URL=http://domain.example/api/



---
Basic API system to handle basic CRUD operations on a Laravel - Mysql Server

pending
-------
hacer que el annotation genere tambien los one to many
el get referenced tiene que permitir = [] en lugar de solo true
el retrieve recursivo para los referenciados tiene que hacerse con get$Entity()