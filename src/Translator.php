<?php

/**
 * Translator class
 *
 * @package   LunchTime
 * @author    J. Oppenlaender
 * @copyright MIT
 * @link      https://github.com/joetm/lunchtime
 * @version   '2017-01'
 *
 */

namespace LunchTime;

use Google\Cloud\Translate\TranslateClient;
use LunchTime\DB;

/**
 * Translator class
 */
class Translator
{
    /** @var array $weekdays Days of the week */
    private $languages = array('en', 'fr', 'zh');

    /** @var object $translationClient Google translation client */
    private $translationClient = null;

    /** @var object $db Database object */
    private $db = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        define('GOOGLEPROJECTID', 'fub-hcc');
        define('GOOGLE_ACCESS_TOKEN', 'ya29.El_MA7vLh1t1lsRdJgMFqucCE9cgxanwfl4BmtN7hBzOvPnUitgwfUbWIyPahDkmdHxGLjfzt6qaFfTYp0fk8k1EQWbWsA59dKI5z6MAgJqT--Z0gKW5cPGNKXM6NM17ng');

        // Instantiate a client
        $this->translationClient = new TranslateClient([
            'projectId' => GOOGLEPROJECTID
        ]);

        $this->db = new DB();
    }

    /**
     * Translate the menu items
     *
     * @param string|array $input  Input to translate
     * @param string|array $target Target language(s)
     *
     * @return boolean|array $output Translated string or array
     */
    public function translate($input, $target = null)
    {
        if ($target) {
            if (is_array($target)) {
                $langs = $target;
            } else {
                $langs = array($target);
            }
        } else {
            $langs = $this->languages;
        }

        $output = false;

        if (is_array($input)) {
            echo 'translating array with ' . count($input) .
                ' items into ' . count($langs) .
                ' languages' . PHP_EOL;
            $output = $this->translateArray($input, $langs);
            // } else {
            // echo 'translating: ' . $input . PHP_EOL;
            // $output = $this->translateString($input, $langs);
        }

        return $output;
    }

    /**
     * Look up the translation in the cache
     *
     * @param string $key Key for lookup
     *
     * @return boolean Query result
     */
    protected function getCachedTranslation($key)
    {
        $sql = "SELECT value from translations WHERE key = '" . $key . "';";
        return $this->db->query($sql);
    }

    /**
     * Store a translation in the cache
     *
     * @param string $key   Key
     * @param string $value Value
     *
     * @return boolean Query result
     */
    protected function storeTranslation($key, $value)
    {
        $sql = "INSERT INTO translations (
                key, value
            )
            VALUES (
                '" . $key . "', '" . $value . "'
            );
        ";
        return $this->db->query($sql);
    }

    /**
     * Translate a string into the target language
     *
     * @param string $text   String to translate
     * @param string $target Target language, e.g. 'en'
     *
     * @return string $translation Translated string
     */
    protected function translateString($text = '', $target = 'en')
    {
        if (!$text) {
            return false;
        }

        // first check the local database if we already translated this string
        $translation = $this->getCachedTranslation($text);

        if (!$translation) {
            // run the translation
            $translation = $this->translationClient->translate($text, [
                'target' => $target
            ]);
            if (!isset($translation['text']) || !$translation['text']) {
                return false;
            }
            $translation = $translation['text'];

            $this->storeTranslation($translation);
        }

        return $translation;
    }

    /**
     * Translate an array of strings
     *
     * @param array $inputArray Array of strings to translate
     * @param array $langs      Target languages, e.g. ['en','de']
     *
     * @return array $translatedArray Translated array
     */
    protected function translateArray(array $inputArray, array $langs = array('en'))
    {
        if (!$inputArray) {
            return false;
        }

        $translatedArray = array();

        foreach ($langs as $lang) {
            $translatedArray[$lang] = array();
        }

        foreach ($langs as $lang) {
            foreach ($inputArray as $key => $txt) {
                $translatedString = $this->translateString($txt, $lang);
                // if no translation could be provided, use the original
                if (!$translatedString) {
                    $translatedString = $txt;
                }
                // maintain the original key
                $translatedArray[$lang][$key] = $translatedString;
            }
        }

        return $translatedArray;
    }
}
