# AVLBL-BE
Built with Laravel 12. 

## Relevant links
- [Laravel 12 documentation](https://laravel.com/docs/12.x/documentation)
- [PHPUnit 11.5 documentation](https://docs.phpunit.de/en/11.5/)

## Getting Started

### Prerequisites
- PHP 8.2+

### Installation
1. Clone the repo
   ```sh
   git clone https://github.com/vooges/avlbl-be.git
   ```
2. Install packages
   ```sh
   composer install
   ```
3. Copy `.env.example` and rename it to `.env`
4. Replace the placeholders in `.env`
   ```bash
   GOOGLE_CLIENT_ID="google-client-id"
   GOOGLE_CLIENT_SECRET="google-client-secret"
   GOOGLE_REDIRECT_URI="uri-to-callback-on-front-end"
   ```
5. Create a database named `avlbl_be` and run the following command to create all the tables and seed the database:
   ```sh
   php artisan migrate:fresh --seed
   ```

### Testing
To run the tests, execute the following command:
```sh
php artisan test
```

### Code Analysis
To run PHPStan, execute the following command:
```sh
composer phpstan
```