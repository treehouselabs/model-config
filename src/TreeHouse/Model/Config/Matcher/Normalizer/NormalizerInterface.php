<?php

namespace TreeHouse\Model\Config\Matcher\Normalizer;

interface NormalizerInterface
{
    /**
     * @param string $value
     *
     * @return string
     */
    public function normalize($value);
}