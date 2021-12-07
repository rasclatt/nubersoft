<?php
namespace Nubersoft;

/**
 * @description 
 */
class Money extends \Nubersoft\nApp
{
    protected static $data;

    public static function toDollar($value, $lang_CO = 'en_US')
    {
        setlocale(LC_MONETARY, $lang_CO . '.UTF-8');
        return money_format('%.2n', $value);
    }
    /**
     * @description 
     */
    public static function getLocaleAbbr($abbr, $lang = false)
    {
        $key = (!empty($lang)) ? implode('_', array_filter([$lang, $abbr])) : self::getLocaleList($abbr);

        if (!is_string($key)) {
            if (count($key) > 1) {
                $msg = 'Warning: this locale has multiple languages.';

                if (isset($key['en_' . $abbr])) {
                    $key = 'en_' . $abbr;
                    $msg .= "{$key} being used.";
                } else
                    $key = key($key);

                trigger_error($msg);
            } else
                $key = key($key);
        }

        return $key;
    }
    /**
     * @description 
     */
    public static function getLocaleList($cou = false, $country = false)
    {
        if (empty(self::$data))
            self::$data = json_decode(file_get_contents(NBR_SETTINGS . DS . 'locale' . DS . 'locales.json'), 1);

        if ($country || $cou) {
            $new = [];
            foreach (self::$data as $locale => $name) {
                if ($cou) {
                    foreach (self::$data as $key => $value) {
                        if (strpos($key, $cou) !== false)
                            $new[$key] = $cou;
                    }
                } else {
                    if (stripos($name, $country) !== false)
                        $new[$locale] = $name;
                }
            }
            return $new;
        }
        return self::$data;
    }
    /**
     * @description 
     */
    public static function toCurrency($value, $country = 'US', $lang = false)
    {
        return self::toDollar($value, self::getLocaleAbbr($country, $lang));
    }
}
