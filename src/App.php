<?php

namespace LunchTime;

use LunchTime\Parser;
use LunchTime\Translator;


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
     * @param string  $inputfile     Path to the file to open
     * @param string  $outputfile    Path to the file to write
     */
    public function init($inputfile, $outputfile)
    {
    	$this->inputfile  = $inputfile;
    	$this->outputfile = $outputfile;
	}

    /**
     * Write everything to the output file
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


		  //   	// set up some arrays to hold the translations
			 //    $this->menuitems[$key]['translations'] = array();
		  //   	$this->menuitems[$key]['translations']['en'] = array();
		  //   	$this->menuitems[$key]['translations']['fr'] = array();
		  //   	$this->menuitems[$key]['translations']['zh'] = array();

		fwrite($w, json_encode(
			$outArray, JSON_PRETTY_PRINT
		));

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
     */
    public function translateMenu()
    {

    	$translator = new Translator();


return;

    	$this->translatedLegend = $translator->translate($this->legend);
    	// var_dump($this->translatedLegend);






		// $translatedMenuItems = $translator->translate($this->menuitems);
  //   	var_dump($translatedMenuItems);



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
        } // foreach
    } // if

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
        } // foreach
    } // if

} // foreach


	}

}
