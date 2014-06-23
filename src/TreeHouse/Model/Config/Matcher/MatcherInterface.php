<?php

namespace TreeHouse\Model\Config\Matcher;

interface MatcherInterface
{
    /**
     * @param string $value
     * @param string $synonym
     *
     * @return integer
     */
    public function match($value, $synonym);

    /**
     * @param string $field
     * @param string $value
     *
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \BadMethodCallException
     *
     * @return array
     */
    public function getMatches($field, $value);
}