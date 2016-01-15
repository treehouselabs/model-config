# Model config

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

This library contains functionality to configure your models with predefined
configuration values, like enums. It also gives you a nice object-oriented way
to handle these.


## Requirements

* PHP >= 5.5
* The stemmer extension: https://github.com/jbboehr/php-stemmer

## Installation

```sh
composer require treehouselabs/model-config
```

## Usage

First, you need to define field configurations. Let's say you have a house model, with a type:

```php

# Project\Model\Article

class House
{
    protected $type;
}
```

The available types are "house", "apartment" and "other". Now, we could introduce an `HouseType` entity,
but that would mostly contain a name, and nothing more. This is where an enum comes in handy. Enums are
classes that have constants pointing to available values. We can create an enum for our type like this:

```php

# Project\Model\Config\Field\HouseType

use TreeHouse\Model\Config\Field\Enum;

class HouseType extends Enum
{
    const HOUSE     = 1;
    const APARTMENT = 2;
    const OTHER     = 3;
}
```

Now you can use these constants to set values and make it readable too:

```php
$house = new House();
$house->setType(HouseType::APARTMENT);
```

### Multiple values
Enums can also be denoted as multivalued fields. For example, our house model could have some facilities:


```php

# Project\Model\Config\Field\Facilities

use TreeHouse\Model\Config\Field\Enum;

class Facilities extends Enum
{
    const ELEVATOR        = 1;
    const ALARM           = 2;
    const AIRCONDITIONING = 3;
    const ROLLER_BLINDS   = 4;

    protected static $multiValued = true;
}
```

Notice how we defined this configuration to be multivalued. The enum itself doesn't do anything with this
information. But it comes in useful in other places, which we'll talk about next.

## Configuration
Now that we have a couple of configurations, we can bundle them in a config object. The config object
uses the (lowercased) constant names mapped to their values. We can use a builder to do this:

```php
$builder = new ConfigBuilder();
$builder->addField('type', HouseType::class);
$builder->addField('facilities', Facilities::class);

$config = $builder->getConfig();
```

The config object provides some convenience methods:

```php
$config->isMultiValued('facilities'); // true
$config->hasFieldConfig('foo'); // false
$config->hasFieldConfigKey('type', 2); // true
$config->hasFieldConfigValue('type', 'apartment'); // true
$config->getFieldConfigValueByKey('type', 2); // 'apartment'
$config->getFieldConfigKey('type', 'apartment'); // 2
```

## Testing

``` bash
composer test
```


## Security

If you discover any security related issues, please email dev@treehouse.nl instead of using the issue tracker.


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.


## Credits

- [Peter Kruithof][link-author]
- [All Contributors][link-contributors]


[ico-version]: https://img.shields.io/packagist/v/treehouselabs/model-config.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/treehouselabs/model-config/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/treehouselabs/model-config.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/treehouselabs/model-config.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/treehouselabs/model-config.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/treehouselabs/model-config
[link-travis]: https://travis-ci.org/treehouselabs/model-config
[link-scrutinizer]: https://scrutinizer-ci.com/g/treehouselabs/model-config/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/treehouselabs/model-config
[link-downloads]: https://packagist.org/packages/treehouselabs/model-config
[link-author]: https://github.com/treehouselabs
[link-contributors]: ../../contributors
