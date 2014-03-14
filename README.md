# karmajobs-com
## Server Stack
- Apache 2.2+ - Web server
- PHP 4.x+ - Scripting language
  - Laravel 4.1.x - Application framework
- Postgres 9.x+ - Relational database

## Front-end Stack
- jQuery 1.x - Client DOM utility and fx framework
- Angular 1.2.x - Client MVC framework
- SCSS (SASS 3.2.x) - CSS preprocessor

## Environment Setup

### OS X / *nix
- Use http://laravel.com/docs/quick#installation

### Windows
- (Optional) Have XAMPP OR WAMP installed
- Download latest 4.1.x release <https://github.com/laravel/laravel/releases>
- Extract package into your designed karmajobs document root. E.g.: `c:/wamp/www/karmajobs-com`
- Download Composer (package dependency manager) <https://getcomposer.org/Composer-Setup.exe>
  - Install as a sibling to `php.exe` which is typically stored under `c:/wamp/bin/php/php<PHP_VERSION>/php.exe`. This is necessary so composer can talk directly to the PHP CLI
- Enable (or install) the following PHP extensions
  - `php_openssl`
  - `php_curl`
  - `php_socket`
- Enable (or instal) the following Apache modules
  - `ssl_module`
- (Optional) enable `openssl` from all `php.ini` files
- Open `cmd.exe` as administrator (command prompt)
- `cd c:/wamp/www/karmajobs-com`
- run `composer install`
- Verify installation on localhost (or whatever your vhost is)

## Production Deployment
## Testing
## Additional Notes
