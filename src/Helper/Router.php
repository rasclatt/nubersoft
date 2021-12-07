<?php
namespace Nubersoft\Helper;

use \Nubersoft\ {
    Dto\Server,
    StringWorks,
    nApp
};

class Router
{
    /**
     * @description Fetches the current host domain
     */
    public static function siteUrl(string $path = ''): string
    {
        $proto = 'http' . ((self::isSsl()) ? 's' : '') . '://';
        $host = (new Server())->HTTP_HOST;
        $domain = '';
        if (defined('BASE_URL')) {
            $domain = (BASE_URL == '{domain}')? $proto . StringWorks::toSingleSlash($host . '/' . $path) : BASE_URL . StringWorks::toSingleSlash('/' . $path);
        } else {
            $domain = $proto . StringWorks::toSingleSlash($host . '/' . $path);
        }
        return $domain;
    }
    /**
     * @description Checks to see if the request is using SSL
     */
    public static function isSsl(): bool
    {
        return (!empty((new Server())->HTTPS));
    }
}