<?php

namespace TreeHouse\Model\Config\Field;

/**
 * Base Enum class
 *
 * Create an enum by implementing this class and adding class constants.
 */
abstract class Enum
{
    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;

    /**
     * @var boolean
     */
    protected static $multiValued = false;

    /**
     * @var array
     */
    protected static $cache = [];

    /**
     * Creates a new value of some type
     *
     * @param  mixed $value
     *
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    final public function __construct($value)
    {
        if (!in_array($value, self::toArray())) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }

        $this->value = $value;
    }

    /**
     * @return string
     */
    final public function getName()
    {
        return array_search($this->value, self::toArray());
    }

    /**
     * @return mixed
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    final public function __toString()
    {
        return (string) $this->value;
    }

    /**
     * Returns all possible values as an array
     *
     * @return array Constant name in key, constant value in value
     */
    final public static function toArray()
    {
        $class = get_called_class();

        if (!isset(self::$cache[$class])) {
            self::$cache[$class] = (new \ReflectionClass($class))->getConstants();
        }

        return self::$cache[$class];
    }

    /**
     * @return boolean
     */
    final public static function isMultiValued()
    {
        return static::$multiValued;
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param  string $name
     * @param  array  $arguments
     *
     * @throws \BadMethodCallException
     *
     * @return static
     */
    final public static function __callStatic($name, $arguments)
    {
        if (defined("static::$name")) {
            return new static(constant("static::$name"));
        }

        throw new \BadMethodCallException("No static method or enum constant '$name' in class " . get_called_class());
    }
}
