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

use LunchTime\NLP;
use LunchTime\Helper;
use LunchTime\SPARQLEngine;

/**
 * Harvester class
 */
class Harvester
{

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

        // jodiertes Salz -> Jodiertes Salz
        $label[0] = strtoupper($label[0]);

        echo 'harvesting legend entry:' . $label . PHP_EOL;

        // $test = Helper::pluralize($label);
        // var_dump($test);
        // exit;

        // instanceof: wdt:P31
        // we know we are querying only food ingredients (wd:Q27643250)
        // => narrow the options, e.g. do not query "Zucker", the album by Rosenstolz
            // ?thing wdt:P31 wd:Q27643250 .

        // Problem: A query for Fisch returns several results (heraldic animal, municipality, familiy name). The entity on Wikipedia is stored as having the label "Fische".
        // Solution: use German Wordnet service (GermaNet)

        // regex version:
            // PREFIX wikibase: <http://wikiba.se/ontology#>
            // SELECT ?thing WHERE {
            // ?thing rdfs:label ?name
            // FILTER(langMatches(lang(?name), "de"))
            // FILTER regex(?name, "^Fisch", "i")
            // }

        // narrowing down the options
                # FILTER EXISTS {
                #     # subclassof ingredient
                #     { ?thing wdt:P279 wd:Q10675206 }
                #     UNION
                #     # instanceof food ingredient
                #     { ?thing wdt:P31 wd:Q27643250 }
                #     UNION
                #     # subclassof food
                #     { ?thing wdt:P279 wd:Q2095 }
                #     UNION
                #     # instanceof thickening agent
                #     { ?thing wdt:P31 wd:Q911138 }
                #     UNION
                #     # subclassof meat
                #     { ?thing wdt:P279 wd:Q10990 }
                # }

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
}
