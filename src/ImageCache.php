<?php

/**
 * Image Cache class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

use LunchTime\Config;

/**
 * Rudimentary image caching
 */
class ImageCache
{
    /**
     * Convert a url into a cache dir path
     *
     * @param string $url URL of the image
     *
     * @return string $localpath Path to the locally stored image in the cache
     */
    public static function getCachePath($url)
    {
        $localpath = sprintf("%s/../%s/%s",
            dirname(__FILE__),
            Config::get('cachedir'),
            basename($url)
        );
        return $localpath;
    }

    /**
     * Acquire image
     *
     * @param string $url URL of the image
     *
     * @return string $localpath Path to the locally stored image in the cache
     */
    public static function acquire($url)
    {
        $localpath = self::getCachePath($url);
        file_put_contents($localpath, file_get_contents($url));
        return $localpath;
    }

    /**
     * Check the cache for an image. Acquire the image if it does not exist.
     *
     * @param string $url URL of the image
     *
     * @return string $path Path to the locally stored image in the cache
     */
    public static function getImage($url)
    {
        $localpath = self::getCachePath($url);
        if (file_exists($localpath)) {
            $path = $localpath;
        } else {
            $path = self::acquire($url);
        }
        return $path;
    }

}
