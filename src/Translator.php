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
use LunchTime\Config;

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
        define('GOOGLE_ACCESS_TOKEN', Config::get('googleaccesstoken'));

        // Instantiate a client
        $this->translationClient = new TranslateClient([
            'projectId' => Config::get('googleprojectid')
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

        return $this->db->queryCache($sql);
    }

    /**
     * Store a translation in the cache
     *
     * @param string $key   Key
     * @param string $value Value
     * @param string $lang  Language
     *
     * @return boolean Query result
     */
    protected function storeTranslation($key, $value, $lang = 'en')
    {
        $sql = "INSERT INTO translations (
                key, value, language
            )
            VALUES (
                '" . $key . "', '" . $value . "', '" . $lang . "'
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

            $this->storeTranslation($text, $translation, $target);
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
