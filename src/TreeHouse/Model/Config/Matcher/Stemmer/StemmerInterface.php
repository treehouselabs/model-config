<?php

namespace TreeHouse\Model\Config\Matcher\Stemmer;

interface StemmerInterface
{
    /**
     * @param string $word
     *
     * @return string
     */
    public function stem($word);
}