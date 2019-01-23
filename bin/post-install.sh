#!/bin/bash

touch /tmp/asdf

#osds config
#cp /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml /var/www/osds/code/api/config/packages/
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/osds_api.yaml ./config/packages/

#nelmio documentation (enable /api/doc url)
#cp /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/packages/nelmio_api_doc.yaml /var/www/osds/code/api/config/packages/nelmio_api_doc.yaml
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/packages/nelmio_api_doc.yaml > ./config/packages/nelmio_api_doc.yaml

#cp /var/www/osds/code/api/vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml /var/www/osds/code/api/config/routes/nelmio_api_doc.yaml
cp ./vendor/osds/api/src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml ./config/routes/nelmio_api_doc.yaml