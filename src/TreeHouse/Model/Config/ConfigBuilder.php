<?php

namespace TreeHouse\Model\Config;

use TreeHouse\Model\Config\Field\Enum;

class ConfigBuilder
{
    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $multiValued = [];

    /**
     * @param string $name
     * @param string $enumClass
     *
     * @throws \InvalidArgumentException
     */
    public function addField($name, $enumClass)
    {
        $refl = new \ReflectionClass($enumClass);
        if (!$refl->isSubclassOf(Enum::class)) {
            throw new \InvalidArgumentException(sprintf('%s is not an instance of %s', $enumClass, Enum::class));
        }

        $values      = $refl->getMethod('toArray')->invoke(null);
        $multiValued = $refl->getMethod('isMultiValued')->invoke(null);

        $this->fields[$name]      = array_flip(array_change_key_case($values, CASE_LOWER));
        $this->multiValued[$name] = $multiValued;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return new Config($this->fields, $this->multiValued);
    }
}