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
$user->attr()->set('is_active', true);
$user->attr()->set('last_seen', now());
$user->attr()->set('favourite_colours','red');
$user->attr()->set('favourite_colours','green');
$user->attr()->set('favourite_colours','blue');
```

Unsetting an attribute
``` php
$user->attr()->unset('is_active');
```

Get an attribute by handle
``` php
$user->attr()->is_active;
=> 1

$user->attr()->favourite_colours;
=> Illuminate\Support\Collection {#944
     all: [
       PWRDK\CustomAttributes\Models\AttributeTypes\AttributeTypeDefault {#984
         value: "red",
       },
       PWRDK\CustomAttributes\Models\AttributeTypes\AttributeTypeDefault {#991
         value: "green",
       },
       PWRDK\CustomAttributes\Models\AttributeTypes\AttributeTypeDefault {#997
         value: "blue",
       },
     ],
   }
```

Update an attribute when there are more than one
``` php
$user
    ->attr()
    ->update('favourite_colours',
      ['value' => 'red'], // Old values
      ['value' => 'orange'] // New Values
    );
```



