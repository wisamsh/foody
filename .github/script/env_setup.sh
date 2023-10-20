#!/bin/sh

# ensure .env file exists
if [ ! -f .env ]; then
    cp .env.example .env
fi

echo "database name: $DB_NAME"
echo "database user: $DB_USER"
echo "database password: $DB_PASSWORD"
echo "database host: $DB_HOST"

# copy secrets to env
# replace data in .env with value from secret
sed -i "s/DB_NAME=database_name/DB_NAME=$DB_NAME/g" .env

sed -i "s/DB_USER=database_user/DB_USER=$DB_USER/g" .env

sed -i "s/DB_PASSWORD=database_password/DB_PASSWORD=$DB_PASSWORD/g" .env

sed -i "s/# DB_HOST=localhost/DB_HOST=$DB_HOST/g" .env

# php setup

# envoy setup