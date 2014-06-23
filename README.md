# Model config

This library contains functionality to configure your models with predefined configuration values, like enums.
It also gives you a nice object-oriented way to handle these.

[![Build Status](https://travis-ci.org/treehouselabs/model-config.svg)](https://travis-ci.org/treehouselabs/model-config)

## Enum
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
