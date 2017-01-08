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
}
