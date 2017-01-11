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
    public function queryByLabel($label)
    {
        // remove blacklisted words from the label before querying
        foreach (Config::get('blacklist') as $blacklistedWord) {
            $label = preg_replace(array(
                "~^" . $blacklistedWord . "\s+~iuU",
                // "~\s+" . $blacklistedWord . "~iuU",
            ), '', $label);
        }

        echo 'harvesting legend entry:' . $label . PHP_EOL;

        // $test = Helper::pluralize($label);
        // var_dump($test);
        // exit;

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

        // MUST have an English label
            // FILTER EXISTS {
            //     ?x rdfs:label ?enLabel .
            //     FILTER(langMatches(lang(?enLabel), "en"))
            // }

        // Transitivity:
        // ?x rdfs:subClassOf* :Animals .

        // Problem: A query for Fisch returns several results (heraldic animal, municipality, familiy name). The entity on Wikipedia is stored as having the label "Fische".

        // Solution: use German Wordnet service (GermaNet)

        // FILTER: only include those entities that have an English label
        // $sparql = "
        //     SELECT ?thing WHERE {
        //         ?thing rdfs:label+ \"{1}\"@de .
        //     }
        // ";

        // regex version:
            // PREFIX wikibase: <http://wikiba.se/ontology#>
            // SELECT ?thing WHERE {
            //     ?thing rdfs:label ?name
            //     FILTER(langMatches(lang(?name), "de"))
            //     FILTER regex(?name, "^Fisch", "i")
            // }

            // PREFIX wikibase: <http://wikiba.se/ontology#>

                // { ?thing rdf:type owl:Class } UNION { ?thing rdf:type owl:Thing }

        // https://www.mediawiki.org/wiki/Wikidata_query_service/User_Manual#Label_service
        $sparql = "
            SELECT ?thing WHERE {
                ?thing rdfs:label+ \"{1}\"@de .
                FILTER EXISTS {
                    ?thing rdfs:label ?langLabel .
                    FILTER(langMatches(lang(?langLabel), \"{2}\"))
                }
            }
        ";

        SPARQLEngine::prepare($sparql, array($label, "en"));

        return SPARQLEngine::get();
    }
}
