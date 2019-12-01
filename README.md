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
         "created_at" => Illuminate\Support\Carbon @1575184900 {#956
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 57,
       ],
     ],
   }

$user->attr()->favourite_colours;
=> Illuminate\Support\Collection {#962
     all: [
       [
         "key" => "favourite_colours",
         "value" => "orange",
         "created_at" => Illuminate\Support\Carbon @1575184900 {#956
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 58,
         "creator" => App\User {#983
           id: 1,
           name: "Rudolf Schwann",
           email: "schwann@damernesmagasin.com",
           email_verified_at: null,
           created_at: "2019-11-26 08:42:25",
           updated_at: "2019-11-26 08:42:25",
         },
       ],
       [
         "key" => "favourite_colours",
         "value" => "green",
         "created_at" => Illuminate\Support\Carbon @1575184900 {#981
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 59,
         "creator" => App\User {#983},
       ],
       [
         "key" => "favourite_colours",
         "value" => "purple",
         "created_at" => Illuminate\Support\Carbon @1575184900 {#988
           date: 2019-12-01 07:21:40.0 UTC (+00:00),
         },
         "id" => 60,
         "creator" => App\User {#983},
       ],
     ],
   }
```

Unsetting an attribute
``` php
$user->attr()->unset('is_active');
```

Updating an attribute is done by referencing the ID directly
``` php
$user->attr()->update(58, ['value' => 'cyan']);
=> PWRDK\CustomAttributes\Models\AttributeTypes\AttributeTypeDefault {#975
     value: "yellow",
   }
```



