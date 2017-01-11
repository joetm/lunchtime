<?php

/**
 * SPARQLEngine class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

use Requests;
use LunchTime\Config;

/**
 * SPARQLEngine class
 */
class SPARQLEngine
{
    /** @var string $endoint SPARQL endpoint */
    private static $endpoint = null;

    /** @var string $url SPARQL endpoint + urlencoded SPARQL query */
    private static $url = null;

    /** @var string $resultFormat Format of the SPARQL result ('object' or 'json') */
    private static $resultFormat = 'json';

    /** @var array $headers Accept header is optional, if the format parameter is already included */
    private static $headers = array('Accept' => 'application/sparql-results+json');

   /**
     * Set the url with the urlencoded SPARQL
     *
     * @param string $sparql Sparql that is to be url-encoded
     */
     private static function setUrl($sparql) {
        if (is_null(self::$endpoint)) {
            self::$endpoint = Config::get('endpoint');
        }
        //
        self::$url = self::$endpoint . urlencode(trim($sparql));
    }

    /**
     * Query SPARQL endpoint
     *
     * @param string $sparql Sparql query
     * @param array  $params Parameters to inject into the query
     */
    public static function prepare($sparql, array $params)
    {
        if (count($params)) {
            // find the occurences of {<NUM>}
            $matches = array();
            $num = preg_match_all("~\{(\d)+\}~u", $sparql, $matches, PREG_OFFSET_CAPTURE);
            // var_dump($num);
            // var_dump($matches);

            if (!$num) {
                self::setUrl($sparql);
            } else {
                // check the parameters array
                $numPlaceholdersFound = count($matches[1]);
                $maxPlaceholder = 0;
                foreach ($matches[1] as $match) {
                    if ($maxPlaceholder < intval($match[0])) {
                        $maxPlaceholder = intval($match[0]);
                    }
                }
                if ($maxPlaceholder > count($params)) {
                    throw new Exception("Missing parameter in SPARQLEngine::prepare()", 1);
                }

                // replace instances of {\d+} with params
                foreach ($matches[1] as $key => $match) {
                    $index = intval($match[0]) - 1;
                    $injectParam = addslashes($params[$index]);
                    $sparql = str_replace($matches[0][$key][0], $injectParam, $sparql);
                }
            }
        }

        // var_dump($sparql);

        // set the url with the urlencoded SPARQL
        self::setUrl($sparql);
    }

    /**
     * Query SPARQL endpoint
     *
     * @return string $match Matched entity URI
     */
    public static function get()
    {
        if (!self::$url) {
            throw new SPARQLEngineException("prepare() must be called before get()", 1);
        }

        $response = Requests::get(self::$url, self::$headers);
        // var_dump($response->status_code);

        if ($response->status_code !== 200) {
            // TODO
            return false;
        }

        $formatAsArray = false;
        if (self::$resultFormat === 'json') {
            $formatAsArray = true;
        }

        // var_dump($response->body);

        return json_decode($response->body, $formatAsArray); // return json body as array
    }
}
