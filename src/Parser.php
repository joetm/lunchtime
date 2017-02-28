<?php

/**
 * Parser class
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
 * Parser class
 */
class Parser
{
    /** @var string $currentday Current day being processed */
    private $currentday = false;

    /** @var integer $working_index Index to keep track of the current array item */
    private $working_index = 0;

    /** @var array $weekdays Days of the week */
    private $weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag');

    /** @var array $legend Legend entries */
    protected $legend = array();

    /** @var string $inputfile File name (or path) to the file that contains the info to parse */
    private $inputfile = null;

    /** @var filepointer $f File handle */
    private $f = null;

    /**
     * Constructor.
     *
     * @param string $inputfile Path to the input txt file to read from
     */
    public function __construct($inputfile)
    {
        // load config
        Config::load();

        $this->inputfile = $inputfile;

        $this->openFile($this->inputfile);
    }

    /**
     * Open a file and return a file pointer
     *
     * @param string $filename Path to the file to open
     * @param string $mode     Options for fopen, e.g. 'rb'
     *
     * @throws \Exception If the file could not be opened
     */
    private function openFile($filename, $mode = 'rb')
    {
        $this->f = fopen($filename, $mode);
        if (!$this->f) {
            throw new \Exception('Could not open file: ' . $filename);
        }
    }

    /**
     * Close the filepointer
     *
     * @return boolean
     */
    public function closeFile()
    {
        if ($this->f) {
            return fclose($this->f);
        }
        return false;
    }

    /**
     * Get the legend entries from the text to find ingredients
     *
     * @return array $legend Legend entries
     */
    public function parseLegend()
    {
        // the legend will look like this:
        // 1    Fleischlos (vegetarisch)            6   Rind/Schweinefleisch           11   Sahne                     16   geschwärzt
        // 2   Schweinefleisch                     7   Schinken /geräuchert           12   Kartoffelstärke           17   jodiertes Salz
        // 3   Rindfleisch                         8   Fisch                          13   Farbstoffe                18   Geschmacksverstärker
        // 4   Geflügelfleisch                     9   Wild                           14   Gemüse-Bouillon           19   Pflanzeneiweiß
        // 5   Lammfleisch                        10   Milch                          15   Zucker                    20   Mit Phosphat

        rewind($this->f);

        while (($line = fgets($this->f)) !== false) {
            // trim the line
            $line = trim($line);

            // skip the line if it's empty
            if ($line === '') {
                continue;
            }

            $matches = array();
            preg_match_all('~(\d\d?[a-züöäß\/\(\)\s\-]+)~iu', $line, $matches);
            if (isset($matches[1])) {
                foreach ($matches[1] as $legendEntry) {
                    $legendEntry = trim($legendEntry);
                    $linematches = array();
                    preg_match('~(\d+)\s+([a-zöäüß0-9\s\-/]+)~iu', $legendEntry, $linematches);
                    if ($linematches) {
                        // var_dump($matches);continue;
                        // $item = array_map('Helper::trimStr', $item);
                        $key = (int) trim($linematches[1]);
                        // it picks up the postal code - there are not more than 30 legend entries -> removes the post code entry
                        if ($key > 30) {
                            continue;
                        }
                        $this->legend[$key] = trim($linematches[2]);
                    }
                }
            }
        }

        // var_dump($legend);

        // write the legend for DEV inspection
        $f = fopen(realpath(__DIR__ . '/../data/legend.json'), 'w');
        if ($f) {
            fwrite($f, json_encode($this->legend, JSON_PRETTY_PRINT));
            echo 'written legend.json' . PHP_EOL;
        }
        fclose($f);

        return $this->legend;
    }

    /**
     * Get the menu entries from the text
     *
     * @return array $menuitems Menu items
     */
    public function parseMenuItems()
    {
        $menuitems = array();

        rewind($this->f);

        $id = 0;
        while (($line = fgets($this->f)) !== false) {
            // reset item array
            $item = array();

            // filter out special characters -> leave only numbers, letters and ().,€
            $line = preg_replace('/[^a-z0-9\-\.\,\(\)äüößÖÜÄ\s€]/iu', '', $line);

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            // echo $line . PHP_EOL;

            // set the day of the week
            if (in_array($line, $this->weekdays)) {
                $this->currentday = $line;
                // echo $item['dow'] . PHP_EOL;
                continue;
            }

            if (is_numeric($line[0]) && $line[1] === '.') {
                // echo $line . PHP_EOL;
                $matches = array();
                preg_match('~\d+\.\s+([a-z0-9\-\.\,\(\)äüößÖÜÄ\s]+)\s+\d+\,\d+~iu', $line, $matches);
                if ($matches) {
                    // var_dump($matches);
                    $item['description'] = trim($matches[1]);
                }
                // Allergiker Warnung?
                // --------------------------------------
                $found = preg_match('~\(\d+\.x\)~iu', $line);
                if ($found) {
                    $item['allergy'] = true;
                } else {
                    $item['allergy'] = false;
                }
                // Side-dish included?
                // --------------------------------------
                $sides = array();
                foreach (Config::get('sidedishes') as $sidedish) {
                    if (strpos($line, $sidedish) !== false) {
                        $sides[] = $sidedish;
                    }
                }
                $item['sidedishes'] = $sides;
            }

            // detect price
            $matches = array();
            preg_match('~(\d+\,\d+)\s*€~', $line, $matches);
            if ($matches) {
                // var_dump($matches);
                $item['price'] = str_replace(',', '.', $matches[1]);
            }

            // if all fields were found, store the item
            if ($this->currentday && isset($item['description']) && isset($item['price'])) {
                // day of the week
                $item['weekday'] = $this->currentday;
                // words
                $item['words'] = array();
                // vegetarisch?
                // --------------------------------------
                if (strpos($item['description'], 'vegetarisch') !== false) {
                    $item['vegetarian'] = true;
                    // remove 'vegetarisch' from the description
                    $item['description'] = trim(str_replace('vegetarisch', '', $item['description']));
                } else {
                    $item['vegetarian'] = false;
                }
                // ingredient key
                // --------------------------------------
                $item['ingredients'] = array();
                $matches = array();
                preg_match('~\(([\d+\.x]+)\)~i', $item['description'], $matches);
                if (isset($matches[1])) {
                    $ingredients_array = explode('.', $matches[1]);
                    foreach ($ingredients_array as $ingredient) {
                        $ingredient = trim($ingredient);
                        // filter out the x, because we check for vegetarian meals later
                        if ($ingredient === '' || $ingredient === 'x') {
                            continue;
                        }
                        // lookup
                        if (isset($this->legend[(int) $ingredient])) {
                            $item['ingredients'][] = $this->legend[(int) $ingredient];
                        }
                        // $item['ingredients'][] = $ingredient;
                    }
                    // remove from description
                    $item['description'] = trim(str_replace($matches[0], '', $item['description']));
                }

                $prep_str = '';
                // contains Zubereitungsart, z.B. "(Bauern Art)"?
                // --------------------------------------
                $matches = array();
                preg_match_all('~(\([a-z0-9\-\s]+\))~iuU', $item['description'], $matches);
                if (isset($matches[1])) {
                    foreach ($matches[1] as $w) {
                        $item['words'][] = trim($w, "() ,.-");
                        $prep_str = str_replace($w, '', $item['description']);
                    }
                }
                if ($prep_str === '') {
                    $prep_str = $item['description'];
                }
                // find words
                // --------------------------------------
                $words = preg_split('~\s~', $prep_str);
                if ($words) {
                    $filtered_words = array();
                    foreach ($words as $index => $word) {
                        if (!$word) {
                            continue;
                        }
                        if (in_array($word, Config::get('blacklist'))) {
                            continue;
                        }
                        if (preg_match('~[\(\d+\.\)]~iU', $word)) {
                            continue;
                        }
                        $filtered_words[] = trim($word, ",");
                    }
                    $item['words'] = array_merge($item['words'], $filtered_words);
                }

                $id++;
                $item['id'] = 'menuitem_' . $id;

                if (!isset($menuitems[$this->working_index])) {
                    // weekday object
                    // $menuitems[$this->currentday] = array( $item );
                    // flat array
                    $menuitems[] = $item;
                    $this->working_index++;
                } else {
                    array_push($menuitems[$this->working_index], $item);
                }
            }
        }

        // var_dump($menuitems);

        return $menuitems;
    }
}
