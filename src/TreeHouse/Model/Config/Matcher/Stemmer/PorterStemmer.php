<?php

namespace TreeHouse\Model\Config\Matcher\Stemmer;

class PorterStemmer implements StemmerInterface
{
    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $encoding;

    /**
     * @param string $language
     * @param string $encoding
     */
    public function __construct($language = 'dutch', $encoding = 'UTF_8')
    {
        $this->language = $language;
        $this->encoding = $encoding;
    }

    /**
     * @param string $word
     *
     * @return string
     */
    public function stem($word)
    {
        return stemword($this->slugify($word), $this->language, $this->encoding);
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    protected function slugify($slug)
    {
        // replace entities with their ascii equivalents
        $slug = html_entity_decode(
            preg_replace(
                '/&([a-z]{1,2})(grave|acute|cedil|circ|ring|tilde|uml|lig|slash|caron|nof|orn|th);/i',
                '$1',
                htmlentities($slug, null, 'UTF-8')
            ),
            null,
            'UTF-8'
        );

        // convert to lowerspace
        $slug = mb_strtolower($slug);

        // perform iconv transliteration, this should cover just about every special character remaining
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $slug);

        // string is now in ascii

        // convert illegal characters to spaces
        $slug = preg_replace('/[^a-z0-9\_\-\s]/', ' ', $slug);

        // trim
        $slug = trim($slug);

        // convert remaining unwanted characters to dashes
        $slug = preg_replace('/[^a-z0-9]/', '-', $slug);

        // convert 2 or more consecutive dashes to one
        $slug = preg_replace('/\-{2,}/', '-', $slug);

        // return rawurlencoded (just to be sure)
        return rawurlencode($slug);
    }
}