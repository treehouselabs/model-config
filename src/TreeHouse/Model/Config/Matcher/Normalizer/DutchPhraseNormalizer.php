<?php

namespace TreeHouse\Model\Config\Matcher\Normalizer;

class DutchPhraseNormalizer implements NormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function normalize($value)
    {
        $translations = [
            '.' => '',
            ',' => '',
            '+' => ' en ',
            '&' => ' en ',
        ];

        return mb_strtolower(strtr($value, $translations));
    }
}