<?php
/*
	Cafeteria Menu Parser
*/

define('VERSION', '1.0');

define('INPUT', 'elokle.txt');
define('OUTPUT', 'dinnermenu.json');

$menuitems = array();
$item = array();
$currentday = false;
$weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag');
$blacklist_words = array('und', 'oder', 'mit', 'in');

function trim_str($the_str, $trim_var = ' ') {
	return trim($the_str, $trim_var);
}


$f = fopen(INPUT, 'rb');


while (($line = fgets($f)) !== false) {

	$item = array();

	// filter out special characters -> leave only numbers, letters and ().,€
	$line = preg_replace('/[^a-z0-9\-\.\,\(\)äüößÖÜÄ\s€]/iu', '', $line);

	$line = trim($line);

	if ($line === '') {
		continue;
	}

	// echo $line . PHP_EOL;

	// set the day of the week
	if (in_array($line, $weekdays)) {
		$currentday = $line;
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
		$found = preg_match('~\(\d+\.x\)~iu', $line);
		if ($found) {
			$item['allergy'] = true;
		} else {
			$item['allergy'] = false;
		}
	}


	// detect price
	$matches = array();
	preg_match('~(\d+\,\d+)\s*€~', $line, $matches);
	if ($matches) {
		// var_dump($matches);
		$item['price'] = $matches[1];
	}


	// if all fields were found, store the item
	if ($currentday && isset($item['description']) && isset($item['price'])) {
		// day of the week
		$item['weekday'] = $currentday;
		// words
		$item['words'] = array();
		// vegetarisch?
		if (strpos($item['description'], 'vegetarisch') !== false) {
			$item['vegan'] = true;
			// remove from description
			$item['description'] = trim(str_replace('vegetarisch', '', $item['description']));
		} else {
			$item['vegan'] = false;
		}
		// ingredient key
		$item['ingredients'] = array();
		$matches = array();
		preg_match('~\(([\d+\.x]+)\)~i', $item['description'], $matches);
		if (isset($matches[1])) {
			$ingredients_array = explode('.', $matches[1]);
			// filter out the x, because we check for vegetarian meals later
			foreach ($ingredients_array as $ingredient) {
				$ingredient = trim($ingredient);
				if ($ingredient === '' || $ingredient === 'x') {
					continue;
				}
				$item['ingredients'][] = $ingredient;
			}
			// remove from description
			$item['description'] = trim(str_replace($matches[0], '', $item['description']));
		}
		//
		$prep_str = '';
		// contains Zubereitungsart, z.B. (Bauern Art)?
		$matches = array();
		preg_match_all('~(\([a-z0-9\-\s]+\))~iuU', $item['description'], $matches);
		if (isset($matches[1])) {
			foreach($matches[1] as $w) {
				$item['words'][] = trim($w, "() ,.");
				$prep_str = str_replace($w, '', $item['description']);
			}
		}
		if ($prep_str === '') {
			$prep_str = $item['description'];
		}
		// find words
		$words = preg_split('~\s~', $prep_str);
		if ($words) {
			$filtered_words = array();
			foreach($words as $index => $word) {
				if (!$word) {
					continue;
				}
				if (in_array($word, $blacklist_words)) {
					continue;
				}
				if (preg_match('~[\(\d+\.\)]~iU', $word)) {
					continue;
				}
				$filtered_words[] = trim($word, ",");
			}
			$item['words'] = array_merge($item['words'], $filtered_words);
		}
		$menuitems[] = $item;
	}

}

// var_dump($menuitems);


// go through the legend to find ingredients
 // 1    Fleischlos (vegetarisch)            6   Rind/Schweinefleisch           11   Sahne                     16   geschwärzt
 //  2   Schweinefleisch                     7   Schinken /geräuchert           12   Kartoffelstärke           17   jodiertes Salz
 //  3   Rindfleisch                         8   Fisch                          13   Farbstoffe                18   Geschmacksverstärker
 //  4   Geflügelfleisch                     9   Wild                           14   Gemüse-Bouillon           19   Pflanzeneiweiß
 //  5   Lammfleisch                        10   Milch                          15   Zucker                    20   Mit Phosphat

rewind($f);

$legend = array();

while (($line = fgets($f)) !== false) {

	$line = trim($line);
	if ($line === '') {
		continue;
	}

	$matches = array();
	preg_match_all('~(\d\d?[a-züöäß\/\(\)\s\-]+)~iu', $line, $matches);
	if (isset($matches[1])) {
		foreach ($matches[1] as $legend_entry) {
			$legend_entry = trim($legend_entry);
			$matches = array();
			preg_match('~(\d+)\s+([a-zöäüß0-9\s\-/]+)~iu', $legend_entry, $matches);
			if ($matches)
			{
				// var_dump($matches);continue;
				// $item = array_map('trim_str', $item);
				// it picks up the postal code - there are not more than 30 legend entries -> removes the post code entry
				$key = (int) trim($matches[1]);
				if ($key > 30) {
					continue;
				}
				$legend[$key] = trim($matches[2]);
			}
		}
	}

}
// var_dump($legend);

fclose($f);

// write to new file
$w = fopen(OUTPUT, 'w');
fwrite($w, json_encode(
	array(
		'menu'   => $menuitems,
		'legend' => $legend,
	), JSON_PRETTY_PRINT
));
echo 'written ' . OUTPUT . PHP_EOL;
fclose($w);



