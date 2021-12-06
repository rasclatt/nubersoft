<?php
namespace Nubersoft\Helper;

use \Nubersoft\Dto\StringWorks\ {
    StringToArrayRequest
};
/**
 * @description Helper class for strings
 */
class StringWorks
{
    /**
     * @description 
     */
    public static function braceReplace(string $string, array $array): string
    {
        $keys = array_keys($array);
        return preg_replace_callback('/{{' . implode('}}|{{', $keys) . '}}/', function ($v) use ($array) {
            return ($array[rtrim(ltrim($v[0], '{{'), '}}')]) ?? false;
        }, $string);
    }
    /**
     * @description 
     */
    public static function toString($element): ?string
    {
        if (is_array($element) || is_object($element))
            return json_encode($element);
        elseif (is_bool($element))
            return ($element) ? "true" : "false";
        else
            return "{$element}";
    }
    /**
     * @description 
     */
    public function toXml($xml): ?string
    {
        return '';
    }
    /**
     * @description 
     * @param 
     */
    public static function stringToArray(StringToArrayRequest $request)
    {
        switch ($request->from) {
            case ('xml'):
                $request->result = json_decode(json_encode(simplexml_load_string($request->input, "SimpleXMLElement", LIBXML_NOCDATA)), 1);
                break;
            case ('json'):
                $request->result = json_decode($request->input, 1);
                break;
            case ('serial'):
                $request->result = unserialize($request->input);
                break;
            case ('file'):
                $request->result = (is_file($request->input)) ? file($request->input) : [];
        }

        if (empty($request->result))
            return (!empty($request->dto)) ? new $request->dto() : [];
        else
            return (!empty($request->dto)) ? new $request->dto($request->result) : $request->result;
    }
    /**
     *	@description    Replaces two slashes with one
     *	@param	$value [string]
     */
    public static function toSingleSlash(string $value): string
    {
        return (string) str_replace('//', '/', $value);
    }
    /**
     *	@description	Same as above but used to replace system specific slashes
     *	@param	$value [string]
     */
    public static function toSingleDs(string $value): string
    {
        return (string) str_replace(DS . DS, DS, $value);
    }
    /**
     *	@description	Turns a lowercase underlined table name to PascalCase
     */
    public static function columnTitleToPascalCase(string $str):? string
    {
        return preg_replace('/[^A-Z]/i', '', str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
    }
}
