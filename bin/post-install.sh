#!/bin/bash

#osds config
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml ./config/packages/osds_api.yaml
mkdir ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds/
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds/* ./config/packages/
cat ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/osds_api.yaml >> ./config/routes/routes.yaml

cat ./vendor/osds/api/src/Infrastructure/Symfony/.env >> .env

#nelmio documentation (enable /api/doc url)
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml ./config/routes/nelmio_api_doc.yaml