<?php

namespace TreeHouse\Model\Config\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\InvalidArgumentException;
use TreeHouse\Model\Config\Config;

class ConfigValueValidator extends ConstraintValidator
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param  integer|integer[] $value
     * @param  Constraint        $constraint
     *
     * @throws InvalidArgumentException
     */
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint ConfigValue */
        $name = $constraint->name;

        // config must exist
        if (!$this->config->hasFieldConfig($name)) {
            throw new InvalidArgumentException(sprintf('There is no config for the field "%s"', $name));
        }

        // value could be null
        if (null === $value) {
            return;
        }

        // check if field is multivalued
        $multiValued = $this->config->isMultiValued($name);

        // if it's multivalued, an array must be given
        if ($multiValued) {
            if (!is_array($value)) {
                $this->context->addViolation($constraint->arrayMessage);

                return;
            }

            // enforce array
            foreach ($value as $key) {
                $this->validateFieldConfig($constraint, $name, $key);
            }
        } else {
            $this->validateFieldConfig($constraint, $name, $value);
        }
    }

    /**
     * @param ConfigValue $constraint
     * @param string      $name
     * @param integer     $value
     */
    protected function validateFieldConfig(ConfigValue $constraint, $name, $value)
    {
        // if it's single valued, value must be numeric
        if (!is_numeric($value)) {
            $this->context->addViolation($constraint->numericMessage, ['{{ value }}' => var_export($value, true)]);

            return;
        }

        // enforce array
        if (!$this->config->hasFieldConfigKey($name, intval($value))) {
            $this->context->addViolation($constraint->message, ['{{ value }}' => $value]);
        }
    }
}
