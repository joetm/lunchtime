<?php

/**
 * Harvester class
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

/**
 * Harvester class
 */
class Harvester
{
    /** @var string $endoint SPARQL endpoint */
    protected $endpoint = null;

    /**
     * Constructor.
     *
     * @param string $endpoint SPARQL REST endpoint
     */
    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * Query the data from the endpoint by its label.
     *
     * @param string $label Label to include in the query
     *
     * @return array Response body
     */
    public function queryByLabel($label)
    {
        // remove blacklisted words from the label before querying
        $label = str_ireplace(Config::get('blacklist'), '', $label);

        echo 'harvesting legend entry:' . $label . PHP_EOL;

        // instanceof: wdt:P31
        // we know we are querying only food ingredients (wd:Q27643250)
        // => narrow the options, e.g. do not query "Zucker", the album by Rosenstolz
                // ?thing wdt:P31 wd:Q27643250 .

        // FILTER NOT EXISTS: do not include disambiguation pages
                // ?thing wdt:P31 wd:Q4167410 .
        // FILTER NOT EXISTS: do not include family names
                // ?thing wdt:P31 wd:Q101352 .

                // FILTER NOT EXISTS { ?thing wdt:P31 wd:Q101352 }
                // FILTER NOT EXISTS { ?thing wdt:P31 wd:Q4167410 }

        $sparql = "
            SELECT ?thing WHERE {
                ?thing rdfs:label \"" . $label . "\"@de .
            }
        ";
        // SERVICE wikibase:label {
        //     bd:serviceParam wikibase:language "de" .
        // }

        // Accept header is optional, since the format parameter is already included
        $headers = array('Accept' => 'application/sparql-results+json');

        $url = $this->endpoint . urlencode(trim($sparql));

        $response = Requests::get($url, $headers);

        if ($response->status_code !== 200) {
            return false;
        }

        return json_decode($response->body, true); // return json body as array
    }
}
