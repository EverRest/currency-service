# Set Up
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
#### Scheduled console command to store current CurrencyRates
```
php artisan app:store-currency-rates
```
#### Scheduled console command to update BankBranches
```
php artisan app:update-bank-branches
