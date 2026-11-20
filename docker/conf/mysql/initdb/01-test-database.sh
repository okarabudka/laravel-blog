#!/bin/sh
# Separate database for running the test suite against MariaDB (make test-mariadb).
mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MARIADB_DATABASE}_test\`;
GRANT ALL PRIVILEGES ON \`${MARIADB_DATABASE}_test\`.* TO '${MARIADB_USER}'@'%';
SQL
