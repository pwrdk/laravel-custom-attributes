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

Publish config file
```php
php artisan vendor:publish --provider="PWRDK\CustomAttributes\CustomAttributesServiceProvider"
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

List all available keys:
``` php
AttributeKey::all()->pluck('handle', 'display_name');
```

Attach the attribute to the Model
``` php
$user->attr()->set('is_active', true);
$user->attr()->set('last_seen', now());
$user->attr()->set('favourite_colours','red');
$user->attr()->set('favourite_colours','green');
$user->attr()->set('favourite_colours','blue');
```

Get an attribute by handle
``` php
$user->attr()->is_active;
=> Illuminate\Support\Collection {#962
     all: [
       [
         "key" => "is_active",
         "value" => 1,
         "creator_id" => null,
         "created_at" => Illuminate\Support\Carbon @1575184861 {#956
           date: 2019-12-01 07:21:01.0 UTC (+00:00),
         },
         "id" => 56,
       ],
     ],
   }

$user->attr()->last_seen;
=> Illuminate\Support\Collection {#962
     all: [
       [
         "key" => "last_seen",
         "value" => Illuminate\Support\Carbon @1574796362 {#960
           date: 2019-11-26 19:26:02.0 UTC (+00:00),
         },
         "creator_id" => null,
         "created_at" => Illuminate\Support\Carbon @1575184900 {#956
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 57,
       ],
     ],
   }

$user->attr()->favourite_colours;
=> Illuminate\Support\Collection {#960
     all: [
       [
         "key" => "favourite_colours",
         "value" => "orange",
         "creator_id" => null,
         "created_at" => Illuminate\Support\Carbon @1575184900 {#948
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 58,
       ],
       [
         "key" => "favourite_colours",
         "value" => "green",
         "creator_id" => null,
         "created_at" => Illuminate\Support\Carbon @1575184900 {#980
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 59,
       ],
       [
         "key" => "favourite_colours",
         "value" => "purple",
         "creator_id" => null,
         "created_at" => Illuminate\Support\Carbon @1575184900 {#986
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 60,
       ],
     ],
   }
```

Unsetting an attribute
``` php
$user->attr()->unset('is_active');
```



