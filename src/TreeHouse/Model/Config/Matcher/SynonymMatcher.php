<?php

namespace TreeHouse\Model\Config\Matcher;

use TreeHouse\Model\Config\Matcher\Normalizer\NormalizerInterface;
use TreeHouse\Model\Config\Matcher\Stemmer\StemmerInterface;

class SynonymMatcher implements MatcherInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var StemmerInterface
     */
    protected $stemmer;

    /**
     * @var array
     */
    protected $synonyms;

    /**
     * @var array
     */
    protected $stopwords;

    /**
     * @param NormalizerInterface $normalizer
     * @param StemmerInterface    $stemmer
     * @param array               $synonyms
     * @param array               $stopwords
     */
    public function __construct(NormalizerInterface $normalizer, StemmerInterface $stemmer, array $synonyms, array $stopwords)
    {
        $this->normalizer = $normalizer;
        $this->stemmer    = $stemmer;
        $this->synonyms   = $synonyms;
        $this->stopwords  = $stopwords;
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return integer|null
     */
    public function match($field, $value)
    {
        $matches = $this->getMatches($field, $value);

        return empty($matches) ? null : $matches[min(array_keys($matches))];
    }

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
    public function getMatches($field, $value)
    {
        if (!$this->supports($field)) {
            throw new \BadMethodCallException(sprintf('Config field "%s" is not supported', $field));
        }

        // don't try to match null value
        if (is_null($value)) {
            return null;
        }

        // we need something we can cast to string
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expecting scalar value for matching "%s", got "%s" instead',
                    $field,
                    gettype($value)
                )
            );
        }

        $synonyms  = $this->getSynonyms($field);
        $stopwords = $this->getStopwords($field);

        $value = (string) $value;

        if ($value === '') {
            return null;
        }

        // compare in lowercase
        $value = $this->normalizer->normalize($value);

        // check for synonyms
        $matches = [];
        foreach ($synonyms as $key => $keySynonyms) {
            foreach ($keySynonyms as $synonym) {
                $distance = $this->getDistance($value, $synonym);
                if ($distance === 0) {
                    // exact match, use this
                    return [0 => $key];
                }

                // it has to be somewhat similar
                if ($distance <= strlen($value) / 3) {
                    $matches[$distance] = $key;
                }

                // try for all stopwords
                foreach ($stopwords as $stopword) {
                    if (($stopword === $value) || false === strpos($value, $stopword)) {
                        // exact stopword match or no match
                        continue;
                    }

                    $distance = $this->getDistance(str_replace($stopword, '', $value), $synonym);
                    if ($distance === 0) {
                        return [0 => $key];
                    }

                    // it has to be somewhat similar
                    if ($distance <= strlen($value) / 3) {
                        $matches[$distance] = $key;
                    }
                }
            }
        }

        return $matches;
    }

    /**
     * @param string $field
     *
     * @return boolean
     */
    public function supports($field)
    {
        return array_key_exists($field, $this->synonyms);
    }

    /**
     * @param string $field
     *
     * @return array
     */
    public function getSynonyms($field)
    {
        return array_key_exists($field, $this->synonyms) ? $this->synonyms[$field] : [];
    }

    /**
     * @param string $field
     *
     * @return array
     */
    public function getStopwords($field)
    {
        return array_key_exists($field, $this->stopwords) ? $this->stopwords[$field] : [];
    }

    /**
     * @param string $value
     * @param string $synonym
     *
     * @return integer
     */
    protected function getDistance($value, $synonym)
    {
        // first try an exact match
        if ($value === $synonym) {
            return 0;
        }

        // try the slugified versions and stem them
        $synonymSlug = $this->stemmer->stem($value);
        $valueSlug   = $this->stemmer->stem($synonym);

        return levenshtein($synonymSlug, $valueSlug);
    }
}
