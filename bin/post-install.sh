#!/bin/bash

#osds config
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml ./config/packages/osds_api.yaml
cat ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/osds_api.yaml >> ./config/routes/routes.yaml

cat ./vendor/osds/api/src/Infrastructure/Symfony/.env >> .env

#sites configurations
ln -s ../sites_configurations sites_configurations

#nelmio documentation (enable /api/doc url)
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml ./config/routes/nelmio_api_doc.yaml