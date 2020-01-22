#!/bin/sh

/var/www/osds/api/bin/console rabbitmq:consumer insert &
/var/www/osds/api/bin/console rabbitmq:consumer update &
/var/www/osds/api/bin/console rabbitmq:consumer delete &
/var/www/osds/api/bin/console rabbitmq:multiple-consumer insert_completed &
/var/www/osds/api/bin/console rabbitmq:multiple-consumer update_completed &
/var/www/osds/api/bin/console rabbitmq:multiple-consumer delete_completed
