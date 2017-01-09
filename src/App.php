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

        return;

        $this->translatedLegend = $translator->translate($this->legend);
        // var_dump($this->translatedLegend);

        // $translatedMenuItems = $translator->translate($this->menuitems);
        // var_dump($translatedMenuItems);

        // translate the title
        foreach ($menuitems as $key => $item) {
            // title translation
            // ---------------------
            // EN
            $menuitems[$key]['translations']['en']['description'] = translate($item['description'], 'en');
            // FR
            $menuitems[$key]['translations']['fr']['description'] = translate($item['description'], 'fr');
            // ZH
            $menuitems[$key]['translations']['zh']['description'] = translate($item['description'], 'zh');

            // keyword translation
            // ---------------------
            if (isset($item['words'])) {
                $menuitems[$key]['translations']['en']['words'] = array();
                $menuitems[$key]['translations']['fr']['words'] = array();
                $menuitems[$key]['translations']['zh']['words'] = array();
                // translate the keywords
                foreach ($item['words'] as $wordkey => $word) {
                    // EN
                    $menuitems[$key]['translations']['en']['words'][$wordkey] = translate($word, 'en');
                    // FR
                    $menuitems[$key]['translations']['fr']['words'][$wordkey] = translate($word, 'fr');
                    // ZH
                    $menuitems[$key]['translations']['zh']['words'][$wordkey] = translate($word, 'zh');
                } //end foreach
            } //end if

            // sidedish translation(s)
            // ---------------------
            if (isset($item['sidedishes']) && $item['sidedishes']) {
                $menuitems[$key]['translations']['en']['sidedishes'] = array();
                $menuitems[$key]['translations']['fr']['sidedishes'] = array();
                $menuitems[$key]['translations']['zh']['sidedishes'] = array();
                foreach ($item['sidedishes'] as $sidedishkey => $sidedish) {
                    // EN
                    $menuitems[$key]['translations']['en']['sidedishes'][$sidedishkey] = translate($sidedish, 'en');
                    // FR
                    $menuitems[$key]['translations']['fr']['sidedishes'][$sidedishkey] = translate($sidedish, 'fr');
                    // ZH
                    $menuitems[$key]['translations']['zh']['sidedishes'][$sidedishkey] = translate($sidedish, 'zh');
                } //end foreach
            } //end if
        } //end foreach
    }

    public function matchWikidata()
    {
        // $harvester = new Harvester('https://query.wikidata.org/sparql?format=json&query=');

        $harvester = new Harvester('http://dbpedia.org/sparql?format=json&query=');

        foreach ($this->legend as $key => $legendEntry) {

            $result = $harvester->queryByLabel($legendEntry);
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
