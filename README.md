
# Technical stack

#### Laravel  Postgres  Redis

# Set Up
#### 1. Run docker-compose containers bundle
###### 
```
docker-compose up --build -d
```
#### 2. Connect to app container
```
docker exec -it currency-service-php-fpm /bin/sh
 or
docker-compose run currency-service-php-fpm {command}
```
#### 3. Run composer install
```
composer install
```
#### 4. Copy .env
```
cp .env.example .env
```
#### 5. Generate app key
```
php artisan key:generate
```
#### 6. Run migrations
```
php artisan migrate
```
#### 7. Run seeds
```
php artisan db:seed
```

# Additional console commands
#### Scheduled console command to store current CurrencyRates
```
php artisan app:store-currency-rates
```
#### Scheduled console command to update BankBranches
```
php artisan app:update-bank-branches
```

### To test notification you need to add mailing provider(for example MAILTRAP) configuration and enable emailing in your .env `MAIL_ENABLED=true`

