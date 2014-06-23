<?php

namespace TreeHouse\Model\Config;

use TreeHouse\Model\Config\Matcher\MatcherInterface;

class ConfigValueGuesser
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @param Config           $config
     * @param MatcherInterface $matcher
     */
    public function __construct(Config $config, MatcherInterface $matcher)
    {
        $this->config  = $config;
        $this->matcher = $matcher;
    }

    /**
     * @param string $type
     *
     * @return boolean
     */
    public function supports($type)
    {
        return $this->config->hasFieldConfig($type);
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @throws \OutOfBoundsException   When given a invalid numeric value
     * @throws \BadMethodCallException When field is not part of the config
     *
     * @return integer|null
     */
    public function guess($field, $value)
    {
        if (!$this->supports($field)) {
            throw new \BadMethodCallException(sprintf('Config field "%s" is not supported', $field));
        }

        $values = $this->getConfigValues($field);

        // already matched?
        if (is_numeric($value)) {
            $value = (int) $value;
            if (!array_key_exists($value, $values)) {
                throw new \OutOfBoundsException(
                    sprintf('Value "%d" for field "%s" already seems a key, but it is not a valid one', $value, $field)
                );
            }

            return $value;
        }

        // look for an exact match first
        if (false !== $key = array_search(mb_strtolower($value), $values)) {
            return $key;
        }

        return $this->matcher->match($field, $value);
    }

    /**
     * @param string $type
     *
     * @return array|null
     */
    protected function getConfigValues($type)
    {
        return $this->config->getFieldConfig($type);
    }
}
