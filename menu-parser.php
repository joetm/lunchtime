<?php
/*
	Cafeteria Menu Parser
*/

define('VERSION', '1.0');

define('INPUT', 'elokle.txt');
define('OUTPUT', 'dinnermenu.json');

$menuitems = array();
$legend = array();
$currentday = false;

$weekdays = array('Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag');
$blacklist_words = array('und', 'oder', 'mit', 'in');
$sidedishes = array('Salat', 'Brot', 'Baguette');

function trim_str($the_str, $trim_var = ' ') {
	return trim($the_str, $trim_var);
}


$f = fopen(INPUT, 'rb');



// var_dump($menuitems);

// go through the legend to find ingredients
 // 1    Fleischlos (vegetarisch)            6   Rind/Schweinefleisch           11   Sahne                     16   geschwärzt
 //  2   Schweinefleisch                     7   Schinken /geräuchert           12   Kartoffelstärke           17   jodiertes Salz
 //  3   Rindfleisch                         8   Fisch                          13   Farbstoffe                18   Geschmacksverstärker
 //  4   Geflügelfleisch                     9   Wild                           14   Gemüse-Bouillon           19   Pflanzeneiweiß
 //  5   Lammfleisch                        10   Milch                          15   Zucker                    20   Mit Phosphat

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

rewind($f);


$id = 0;
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
		foreach ($sidedishes as $sidedish) {
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
	if ($currentday && isset($item['description']) && isset($item['price'])) {
		// day of the week
		$item['weekday'] = $currentday;
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
			// filter out the x, because we check for vegetarian meals later
			foreach ($ingredients_array as $ingredient) {
				$ingredient = trim($ingredient);
				if ($ingredient === '' || $ingredient === 'x') {
					continue;
				}
				// lookup
				if (isset($legend[(int) $ingredient])) {
					$item['ingredients'][] = $legend[(int) $ingredient];
				}
				// $item['ingredients'][] = $ingredient;
			}
			// remove from description
			$item['description'] = trim(str_replace($matches[0], '', $item['description']));
		}
		//
		$prep_str = '';
		// contains Zubereitungsart, z.B. (Bauern Art)?
		// --------------------------------------
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
		// --------------------------------------
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
		$id++;
		$item['id'] = 'menuitem_' . $id;
		$menuitems[] = $item;
	}

}

fclose($f);

// ------------------------------------
// Translations
// ------------------------------------

define('GOOGLEPROJECTID', 'fub-hcc');
define('GOOGLE_ACCESS_TOKEN', 'ya29.El_MA7vLh1t1lsRdJgMFqucCE9cgxanwfl4BmtN7hBzOvPnUitgwfUbWIyPahDkmdHxGLjfzt6qaFfTYp0fk8k1EQWbWsA59dKI5z6MAgJqT--Z0gKW5cPGNKXM6NM17ng');


require __DIR__ . '/vendor/autoload.php';
use Google\Cloud\Translate\TranslateClient;

# Instantiate a client
$translationClient = new TranslateClient([
    'projectId' => GOOGLEPROJECTID
]);

function translate($text = '', $target = 'en') {
    global $translationClient;
    if (!$text) {
        return '';
    }
    // run the translation
    $translation = $translationClient->translate($text, [
        'target' => $target
    ]);
    if (!isset($translation['text']) || !$translation['text']) {
        return '';
    }
    return $translation['text'];
}

// translate the menu
foreach ($menuitems as $key => $item) {

    # The text to translate
    $text = $item['description'];

    // EN
    $menuitems[$key]['description_en'] = translate($text, 'en');
    // FR
    $menuitems[$key]['description_fr'] = translate($text, 'fr');
    // ZH
    $menuitems[$key]['description_zh'] = translate($text, 'zh');

} // foreach



// ------------------------------------
// write to new file
// ------------------------------------
$w = fopen(OUTPUT, 'w');
fwrite($w, json_encode(
	array(
		'menu'   => $menuitems,
		// 'legend' => $legend,
	), JSON_PRETTY_PRINT
));
echo 'written ' . OUTPUT . PHP_EOL;
fclose($w);
// ------------------------------------

