<?php

namespace TreeHouse\Model\Config\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConfigValue extends Constraint
{
    public $name;
    public $message = 'Invalid config value: {{ value }}';
    public $arrayMessage = 'Config is multivalue but no array was given';
    public $numericMessage = 'Expecting a numeric value, but got {{ value }}';

    public function validatedBy()
    {
        return 'config_value';
    }
}
