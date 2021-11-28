<?php
namespace Nubersoft;

use \Nubersoft\Dto\StringWorks\StringToArrayRequest;

/**
 * @description 
 */
class StringWorks extends nApp
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
}
