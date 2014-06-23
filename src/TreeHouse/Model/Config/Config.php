<?php

namespace TreeHouse\Model\Config;

/**
 * Class containing configuration for models.
 */
class Config
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $multiValued;

    /**
     * @param array $config
     * @param array $multiValued
     */
    public function __construct(array $config, array $multiValued = [])
    {
        $this->config      = $config;
        $this->multiValued = $multiValued;
    }

    /**
     * Returns the complete configuration.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Returns the multiValued state of fields.
     *
     * @return array
     */
    public function getMultiValued()
    {
        return $this->multiValued;
    }

    /**
     * Checks if a field is multi-valued
     *
     * @param  string  $name
     *
     * @return boolean
     */
    public function isMultiValued($name)
    {
        return $this->multiValued[$name];
    }

    /**
     * Checks if a configuration exists for a given field
     *
     * @param  string  $name
     *
     * @return boolean
     */
    public function hasFieldConfig($name)
    {
        return array_key_exists($name, $this->getConfig());
    }

    /**
     * Returns the configuration for a field, if it exists
     *
     * @param  string     $name
     *
     * @return array|null
     */
    public function getFieldConfig($name)
    {
        $config = $this->getConfig();

        return array_key_exists($name, $config) ? $config[$name] : null;
    }

    /**
     * Checks if an key exists for a given field config
     *
     * @param  string  $config
     * @param  integer $key
     *
     * @return boolean
     */
    public function hasFieldConfigKey($config, $key)
    {
        if (null === $values = $this->getFieldConfig($config)) {
            return false;
        }

        return array_key_exists($key, $values);
    }

    /**
     * Checks if a name-value exists for a given field config
     *
     * @param  string  $field
     * @param  string  $value
     *
     * @return boolean
     */
    public function hasFieldConfigValue($field, $value)
    {
        if (null === $values = $this->getFieldConfig($field)) {
            return false;
        }

        return false !== array_search($value, $values);
    }

    /**
     * Returns the name for a field config value
     *
     * @param  string      $config
     * @param  integer     $key
     *
     * @return string|null
     */
    public function getFieldConfigValueByKey($config, $key)
    {
        if (null === $values = $this->getFieldConfig($config)) {
            return null;
        }

        return array_key_exists($key, $values) ? $values[$key] : null;
    }

    /**
     * Returns the key for a field config name-value
     *
     * @param  string       $config
     * @param  string       $value
     *
     * @return integer|null
     */
    public function getFieldConfigKey($config, $value)
    {
        if (null === $values = $this->getFieldConfig($config)) {
            return null;
        }

        return array_search($value, $values);
    }
}
