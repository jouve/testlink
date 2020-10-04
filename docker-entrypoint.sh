#!/bin/sh

if [ ! -f config_db.inc.php ]; then
	[ -z "$DB_TYPE" ] && echo "Missing DB_TYPE" && exit 1
	[ -z "$DB_USER" ] && echo "Missing DB_USER" && exit 1
	[ -z "$DB_PASS" ] && echo "Missing DB_PASS" && exit 1
	[ -z "$DB_HOST" ] && echo "Missing DB_HOST" && exit 1
	[ -z "$DB_NAME" ] && echo "Missing DB_NAME" && exit 1

	cat <<EOF > config_db.inc.php
<?php
define('DB_TYPE', '$DB_TYPE');
define('DB_USER', '$DB_USER');
define('DB_PASS', '$DB_PASS');
define('DB_HOST', '$DB_HOST');
define('DB_NAME', '$DB_NAME');
define('DB_TABLE_PREFIX', '$DB_TABLE_PREFIX');
EOF
fi

exec httpd -DFOREGROUND
