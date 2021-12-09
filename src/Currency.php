<?php
namespace Nubersoft;

use \Nubersoft\nApp;
use \Nubersoft\Dto\{
    StringWorks\StringToArrayRequest,
    Currency\MoneyFormatsResponse,
    Currency\ToMoneyRequest
};
use Nubersoft\Dto\Currency\ToDollarRequest;
/**
 * @description Access to some base currency functions with an optional stripped-down API connection to fixer.io
 */
class Currency
{
    protected string $queryString;
    protected ?string $endpoint;
    protected ?string $fixerApiKey;
    protected $response, $rates, $currency, $baseCurrency;

    const DEFAULT_API = 'https://data.fixer.io/api/';
    const BASE_CURRENCY = 'USD';
    /**
     *	@description	Sets up a default (or not) connection to fixer.io
     */
    public function __construct(string $endpoint = null, string $fixerApiKey = null)
    {
        $this->endpoint = ((empty($endpoint))? self::DEFAULT_API : $endpoint);
        $this->fixerApiKey = $fixerApiKey;
    }

    public function setBaseCurrency($currency)
    {
        $this->baseCurrency = $currency;
        return $this;
    }

    public function getBaseCurrency()
    {
        return (!empty($this->baseCurrency)) ? $this->baseCurrency : self::BASE_CURRENCY;
    }

    public function setAttributes($attr)
    {
        $this->queryString = http_build_query($attr);
        return $this;
    }
    /**
     *	@description	
     *	@param	
     */
    public function query(string $string)
    {
        $this->response = file_get_contents($string);
        return $this;
    }

    public function fetch()
    {
        if (empty($this->endpoint)) {
            $this->endpoint = self::DEFAULT_API;
            $this->setAttributes(array('base' => $this->getBaseCurrency()));
        }

        $this->query($this->endpoint . '?access_key=' . $this->fixerApiKey . $this->queryString);

        $this->queryString = false;
        return $this;
    }
    /**
     *	@description	
     *	@param	
     */
    public function getResponse(bool $json = true)
    {
        return ($json)? json_decode($this->response, 1) : $this->response;
    }

    public function getRates($get = false)
    {
        $response = $this->getResponse(true);
        if (!empty($get)) {
            if ($get == $this->getBaseCurrency())
                return 1;

            return (!empty($response['rates'][$get])) ? $response['rates'][$get] : false;
        }

        return (isset($response['rates'])) ? $response['rates'] : array();
    }

    public function convert($array)
    {
        $this->setBaseCurrency($array['from'])->fetch();

        $to = $array['to'];
        $rate   = $this->getRates($to);
        $array['value'] = preg_replace('/[^\d\.]/', '', $array['value']);
        return $array['value'] * $rate;
    }

    public function getLocale($country)
    {
        $locales = $this->getLocaleList();
        return (isset($locales[$country]['lang'])) ? $locales[$country]['lang'] : 'en_US';
    }

    public function getLocaleList($key = 'abbr3')
    {
        $path = NBR_SETTINGS . DS . 'locale' . DS . 'locale_list.xml';
        $reg = nApp::call()->toArray(simplexml_load_file($path));
        return ArrayWorks::organizeByKey($reg['locale'], $key, ['unset' => false]);
    }
    /**
     * @description Fetches all the available international money formats from xml file
     */
    public function getMoneyFormats(): array
    {
        $request = new StringToArrayRequest();
        $request->from = 'xml';
        $request->input = @file_get_contents(__DIR__ . DS . 'Currency' . DS . 'Core' . DS . 'settings' . DS . 'locale_list.xml');
        return array_map(function ($v) {
            return new MoneyFormatsResponse($v);
        }, StringWorks::stringToArray($request)['locale']);
    }

    public static function toMoney(ToMoneyRequest $request)
    {
        if (!class_exists('NumberFormatter')) {
            throw new \Exception('"NumberFormatter" is required to be installed. You need install "php-intl"', 500);
        }
        $fmt = new \NumberFormatter(...[
            "{$request->language}_{$request->country}",
            \NumberFormatter::CURRENCY
        ]);
        $str = $fmt->formatCurrency($request->number, $request->to);
        return $str;
    }

    public static function toDollar(
        float $number,
        string $to = 'USD',
        string $country = 'USA'
    ) {
        $format = null;

        array_map(function ($v) use ($country, $format) {
            if ($v->abbr3 == $country)
                $format = $v;
        }, (new Currency())->getMoneyFormats());

        $dto = new ToDollarRequest([
            'number' => $number,
            'format' => $format,
            'to' => $to
        ]);

        return self::toMoney($dto);
    }
}