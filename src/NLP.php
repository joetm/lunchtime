<?php

/**
 * NLP tools class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

/**
 * NLP class
 */
class NLP
{
    /** @var string $pluralizer The pluralizer tool to use */
    protected $pluralizer = null;

    /** @var string $languages The language */
    protected $languages = array('de', 'en', 'fr');

    /**
     * Constructor.
     */
    public function __construct($pluralizer = 'pattern')
    {
        $this->$pluralizer = $pluralizer;
    }

    /**
     * Pluralize a string
     *
     * @param string $word Input string to pluralize
     *
     * @return array If no plural was generated, returns an array with the singular. Otherwise, returns an array with singular and plural.
     */
    public static function pluralize($word, $lang)
    {
        $output = array($word);
        // find the plural of the input

        if (!in_array($lang, $this->languages)) {
            $lang = 'de';
        }

        $plural = null;
        $command = __DIR__ . "/../tools/" . $lang . "/NLP/pluralize.py" . " " . escapeshellarg($word);
        $plural = exec($command, $cmd, $return);
        if ($return > 0) {
            return $output;
        }
        array_push($output, $plural);
        return $output;
    }
}
