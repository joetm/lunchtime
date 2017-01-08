<?php

/**
 * Cafeteria Menu Parser
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

error_reporting(-1); // report ALL errors

require __DIR__ . '/vendor/autoload.php';

use LunchTime\Helper;
use LunchTime\App;

define('INPUT', __DIR__ . '/data/elokle.txt');
define('OUTPUT', __DIR__ . '/data/dinnermenu.json');

$app = new App();

// init the app with the input and output files
$app->init(INPUT, OUTPUT);

// get the menu items and the legend entries from the text file
$app->parseMenu();

// translate the menu items
// $app->translateMenu();

echo 'OK.' . PHP_EOL;

// write everything to the output file
$app->writeOutput();
