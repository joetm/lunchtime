<?php

/**
 * Helper class
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
 * Helper class
 */
class Helper
{

    /**
     * Trim a string
     *
     * @param string $the_str  The string to trim
     * @param string $trim_var Optional trim characters in a string
     *
     * @return string Trimmed string
     */
    public static function trimStr($the_str, $trim_var = ' ')
    {
        return trim($the_str, $trim_var);
    }

    /**
     * Pluralize a string
     *
     * @param string $word Input string to pluralize
     *
     * @return array If no plural was generated, returns an array with the singular. Otherwise, returns an array with singular and plural.
     */
    public static function pluralize($word)
    {
        $output = array($word);
        // find the plural of the input

        $plural = null;
        $command = realpath(__DIR__ . "/../NLP/pluralize.py") . " " . escapeshellarg($word);
        $plural = exec($command, $cmd, $return);
        if ($return > 0) {
            return $output;
        }
        array_push($output, $plural);
        return $output;
    }
}
