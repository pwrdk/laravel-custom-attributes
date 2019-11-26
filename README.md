# laravel-custom-attributes
A custom attributes package for Eloquent Models.

## Installation
You can install the package via composer:

```bash
composer require pwrdk/laravel-custom-attributes
```

## Migrate
```php
php artisan:migrate
```

## Seed

Install basic Attribute Types (text, number, boolean, datetime and contact info)

```php
php artisan db:seed --class="PWRDK\CustomAttributes\Seeds\DatabaseSeeder"
```

## Usage
Add HasCustomAttributes trait to Any Eloquent Model
``` php
use PWRDK\CustomAttributes\HasCustomAttributes;
use HasCustomAttributes;
```

Create a new attribute key
``` php
use PWRDK\CustomAttributes\CustomAttributes;
CustomAttributes::createKey('is_active', 'Is Active', 'boolean', true);
CustomAttributes::createKey('last_seen', 'Last seen', 'datetime', true);
CustomAttributes::createKey('favourite_colours', 'Favourite Colours', 'text', false);
```

Attach the attribute to the Model
``` php
$user->attr('is_active')->set(true);
$user->attr('last_seen')->set(now());
$user->attr('favourite_colours')->set('red');
$user->attr('favourite_colours')->set('green');
$user->attr('favourite_colours')->set('blue');
```

Get and use the attributes
``` php
$user->attr('is_active')->get();
$user->attr('favourite_colours')->get();
```
