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

use \ImageCache;

use LunchTime\NLP;
use LunchTime\Helper;
use LunchTime\SPARQLEngine;

/**
 * Harvester class
 */
class Harvester
{

    /**
     * Prepare a string
     *
     * @param string $str String label
     *
     * @return string $str Prepared label
     */
    private function prepareString($str)
    {
        // e.g. jodiertes Salz -> Jodiertes Salz
        $str[0] = strtoupper($str[0]);
        return $str;
    }

    /**
     * Query the data from the endpoint by its label.
     *
     * @param string $label Label to include in the query
     *
     * @return array Response body
     */
    public function queryURIByLabel($label)
    {
        // basic removal of blacklisted words from the label before querying
        foreach (Config::get('blacklist') as $blacklistedWord) {
            $label = preg_replace(array(
                "~^" . $blacklistedWord . "\s+~iuU",
                // "~\s+" . $blacklistedWord . "~iuU",
            ), '', $label);
        }

        $label = $this->prepareString($label);

        echo 'harvesting legend entry:' . $label . PHP_EOL;

        // TODO
        // Problem: A query for Fisch returns several results (heraldic animal, municipality, familiy name). The entity on Wikipedia is stored as having the label "Fische".
        // Solution: use German Wordnet service (GermaNet)
        // TODO
        // $test = Helper::pluralize($label);
        // var_dump($test);
        // exit;

        // regex version:
            // PREFIX wikibase: <http://wikiba.se/ontology#>
            // SELECT ?thing WHERE {
            // ?thing rdfs:label ?name
            // FILTER(langMatches(lang(?name), "de"))
            // FILTER regex(?name, "^Fisch", "i")
            // }

        // TODO
        // https://www.mediawiki.org/wiki/Wikidata_query_service/User_Manual#Label_service

        $sparql = "
            PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
            SELECT ?thing WHERE {
                {
                    ?thing rdfs:label* \"{1}\"@de
                }
                UNION
                { ?thing skos:altLabel \"{1}\"@de }
                # MUST have a label in the given language
                FILTER EXISTS {
                    ?thing rdfs:label ?langLabel .
                    FILTER(langMatches(lang(?langLabel), \"{2}\"))
                }
                # result must be...
                # FILTER EXISTS {
                #     # sublass of food
                #     { ?thing wdt:P279* wd:Q2095 }
                #     UNION # or
                #     # instanceof food ingredient
                #     { ?thing wdt:P31* wd:Q27643250 }
                # }
                # filter out disambiguation pages
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q4167410 .
                }
                # don't include category pages
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q4167836 .
                }
                # exclude family names
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q101352 .
                }
                # no music albums and singles and 'extended play'
                FILTER NOT EXISTS {
                    { ?thing wdt:P31 wd:Q482994 }
                    UNION
                    { ?thing wdt:P31 wd:Q134556 }
                    UNION
                    { ?thing wdt:P31 wd:Q169930 }
                }
                # no persons
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q5
                }
                # no movies
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q11424
                }
                # no heraldic animals
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q350945
                }
            }
        ";

        SPARQLEngine::prepare($sparql, array($label, "en"));

        return SPARQLEngine::get();
    }

    /**
     * Query the wikidata image from the endpoint by a given label of an entity
     *
     * @param string $label Label to include in the query
     *
     * @return string Image
     */
    public function queryImageByLabel($label)
    {
        $label = $this->prepareString($label);

        echo 'harvesting image for entry:' . $label . PHP_EOL;

        // https://www.mediawiki.org/wiki/Wikidata_query_service/User_Manual#Label_service

        $sparql = "
            PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
            SELECT ?image WHERE {
                {
                    ?thing rdfs:label* \"{1}\"@de .
                    ?thing wdt:P18 ?image .
                }
                UNION
                {
                    ?thing skos:altLabel \"{1}\"@de
                }
                # MUST have a label in the given language
                FILTER EXISTS {
                    ?thing rdfs:label ?langLabel .
                    FILTER(langMatches(lang(?langLabel), \"{2}\"))
                }
                # filter out disambiguation pages
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q4167410 .
                }
                # don't include category pages
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q4167836 .
                }
                # exclude family names
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q101352 .
                }
                # no music albums and singles and 'extended play'
                FILTER NOT EXISTS {
                    { ?thing wdt:P31 wd:Q482994 }
                    UNION
                    { ?thing wdt:P31 wd:Q134556 }
                    UNION
                    { ?thing wdt:P31 wd:Q169930 }
                }
                # no persons
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q5 .
                }
                # no movies
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q11424 .
                }
                # no heraldic animals
                FILTER NOT EXISTS {
                    ?thing wdt:P31 wd:Q350945 .
                }
            }
        ";

        SPARQLEngine::prepare($sparql, array($label, "en"));

        $result = SPARQLEngine::get();
        // var_dump($result);

        // did we find an image?

        // no bueno
        if (!isset($result['results']['bindings'][0]['image']['value'])) {
            return null;
        }

        // no bueno
        $imgsrc = $result['results']['bindings'][0]['image']['value'];

        echo '  Found ' . $label . ' : ' . $imgsrc . PHP_EOL;

        // download and cache image
        $imagecache = new ImageCache();
        // no bueno
        $imagecache->cached_image_directory = dirname(__FILE__) . '/../cache';

        $cached_src = $imagecache->cache($imgsrc);

        return $cached_src;
    }
}
