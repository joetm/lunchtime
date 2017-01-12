<?php

/**
 * LunchTime Main App
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

use LunchTime\Parser;
use LunchTime\Config;
use LunchTime\Harvester;
use LunchTime\Translator;

/**
 * Application class
 */
class App
{
    /** @var array $menuitems Menu items */
    protected $menuitems = array();
    /** @var array $legend Legend entries */
    protected $legend = array();
    /** @var array $translatedLegend Translated legend entries */
    protected $translatedLegend = array();

    /** @var string $inputfile Input filename */
    private $inputfile = null;
    /** @var string $outputfile Output filename */
    private $outputfile = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        define('VERSION', '1.0');
    }

    /**
     * Set the input and output files
     *
     * @param string $inputfile  Path to the file to open
     * @param string $outputfile Path to the file to write
     */
    public function init($inputfile, $outputfile)
    {
        $this->inputfile  = $inputfile;
        $this->outputfile = $outputfile;
    }

    /**
     * Write everything to the output file
     *
     * @throws Exception If outputfile could not be opened
     */
    public function writeOutput()
    {
        $w = fopen($this->outputfile, 'w');
        if (!$w) {
            throw new Exception("Could not write to output file", 1);
        }

        $outArray = array();

        if ($this->menuitems) {
            $outArray['menu'] = $this->menuitems;
        }


        // // set up some arrays to hold the translations
        // $this->menuitems[$key]['translations'] = array();
        // $this->menuitems[$key]['translations']['en'] = array();
        // $this->menuitems[$key]['translations']['fr'] = array();
        // $this->menuitems[$key]['translations']['zh'] = array();

        fwrite($w, json_encode($outArray, JSON_PRETTY_PRINT));

        echo 'written ' . basename($this->outputfile) . PHP_EOL;

        fclose($w);
    }

    /**
     * Run the parser
     */
    public function parseMenu()
    {
        $parser = new Parser($this->inputfile);

        echo 'parsing ' . basename($this->inputfile) . '...' . PHP_EOL;

        $this->legend = $parser->parseLegend();
        // var_dump($this->legend);

        echo 'legend: OK.' . PHP_EOL;

        $this->menuitems = $parser->parseMenuItems();

        echo 'menuparsing: OK.' . PHP_EOL;

        $parser->closeFile();
    }

    /**
     * Run the translator
     *
     * @return
     */
    public function translateMenu()
    {
        $translator = new Translator();

        $this->translatedLegend = $translator->translate($this->legend);
        // var_dump($this->translatedLegend);

        // var_dump($this->translatedLegend);
        // exit;
        // return;

        // $translatedMenuItems = $translator->translate($this->menuitems);
        // var_dump($translatedMenuItems);

        // translate the title
        foreach ($this->menuitems as $key => $item) {
            // title translation
            // ---------------------
            // EN
            $this->menuitems[$key]['translations']['en']['description'] = $translator->translate($item['description'], 'en');
            // FR
            $this->menuitems[$key]['translations']['fr']['description'] = $translator->translate($item['description'], 'fr');
            // ZH
            $this->menuitems[$key]['translations']['zh']['description'] = $translator->translate($item['description'], 'zh');

            // keyword translation
            // ---------------------
            if (isset($item['words'])) {
                $this->menuitems[$key]['translations']['en']['words'] = array();
                $this->menuitems[$key]['translations']['fr']['words'] = array();
                $this->menuitems[$key]['translations']['zh']['words'] = array();
                // translate the keywords
                foreach ($item['words'] as $wordkey => $word) {
                    // EN
                    $this->menuitems[$key]['translations']['en']['words'][$wordkey] = $translator->translate($word, 'en');
                    // FR
                    $this->menuitems[$key]['translations']['fr']['words'][$wordkey] = $translator->translate($word, 'fr');
                    // ZH
                    $this->menuitems[$key]['translations']['zh']['words'][$wordkey] = $translator->translate($word, 'zh');
                } //end foreach
            } //end if

            // sidedish translation(s)
            // ---------------------
            if (isset($item['sidedishes']) && $item['sidedishes']) {
                $this->menuitems[$key]['translations']['en']['sidedishes'] = array();
                $this->menuitems[$key]['translations']['fr']['sidedishes'] = array();
                $this->menuitems[$key]['translations']['zh']['sidedishes'] = array();
                foreach ($item['sidedishes'] as $sidedishkey => $sidedish) {
                    // EN
                    $this->menuitems[$key]['translations']['en']['sidedishes'][$sidedishkey] = $translator->translate($sidedish, 'en');
                    // FR
                    $this->menuitems[$key]['translations']['fr']['sidedishes'][$sidedishkey] = $translator->translate($sidedish, 'fr');
                    // ZH
                    $this->menuitems[$key]['translations']['zh']['sidedishes'][$sidedishkey] = $translator->translate($sidedish, 'zh');
                } //end foreach
            } //end if
        } //end foreach
    }

    /**
     * Match entities on wikiData
     *
     * @param string $inputfile  Path to the file to open
     * @param string $outputfile Path to the file to write
     */
    public function matchWikidata()
    {
        $harvester = new Harvester('https://query.wikidata.org/sparql?format=json&query=');

        // note: dbpedia also returns wikidata entities (sameAs relationships)
        // $harvester = new Harvester('http://dbpedia.org/sparql?format=json&query=');

        foreach ($this->legend as $key => $legendEntry) {

            $result = $harvester->queryURIByLabel($legendEntry);
            // var_dump($result);
            // var_dump($result['results']['bindings']);

            if (count($result['results']['bindings']) > 0) {

                echo "found " . count($result['results']['bindings']) . " results" . PHP_EOL;

                foreach ($result['results']['bindings'] as $key => $binding) {
                    echo " - " . $binding['thing']['value'] . PHP_EOL;
                }
            }
        }
    }
}
