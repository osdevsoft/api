#!/bin/bash

#osds config
cp /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml /var/www/osds/code/api/config/packages/
#cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml ./config/packages/

#nelmio documentation (enable /api/doc url)
cat /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/packages/nelmio_api_doc.yaml > /var/www/osds/code/api/config/packages/nelmio_api_doc.yaml
#cat ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/nelmio_api_doc.yaml > ./config/packages/nelmio_api_doc.yaml

cat /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml >> /var/www/osds/code/api/config/routes/nelmio_api_doc.yaml
#cat ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml >> ./config/routes/nelmio_api_doc.yaml