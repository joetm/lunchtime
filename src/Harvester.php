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

    public function queryByLabel($label)
    {
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
