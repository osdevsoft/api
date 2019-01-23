#!/bin/bash

#osds config
cp ../src/Infrastructure/Symfony/config/packages/osds_api.yaml ../../../../config/packages/

#nelmio documentation (enable /api/doc url)
cat ../src/Infrastructure/Symfony/config/packages/nelmio_api_doc.yaml > ../../../../config/packages/nelmio_api_doc.yaml
cat ../src/Infrastructure/Symfony/config/routes/nelmio_api_doc.yaml >> ../../../../config/routes/nelmio_api_doc.yaml