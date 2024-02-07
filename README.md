# Stack: Laravel 10, Nginx, Postgres, PHP-FPM, Docker
# How to use it?
#### Run docker container
###### 
```
docker-compose up --build -d
```
#### Connect to app container
```
docker exec -it currency-service-php-fpm /bin/sh
```
#### Run composer install
```
composer install
```
#### Copy .env
```
cp .env.example .env
```
#### Generate key
```
php artisan key:generate
```
#### Run migration and seed
```
php artisan migrate --seed
```
