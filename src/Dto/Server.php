<?php
namespace Nubersoft\Dto;

class Server extends \SmartDto\Dto
{
    public string $CONTEXT_DOCUMENT_ROOT = '';
    public string $CONTEXT_PREFIX = '';
    public string $DOCUMENT_ROOT = '';
    public string $DYLD_LIBRARY_PATH = '';
    public string $GATEWAY_INTERFACE = '';
    public string $HTTPS = '';
    public string $HTTP_ACCEPT = '';
    public string $HTTP_ACCEPT_ENCODING = '';
    public string $HTTP_ACCEPT_LANGUAGE = '';
    public string $HTTP_AUTHORIZATION = '';
    public string $HTTP_CONNECTION = '';
    public string $HTTP_COOKIE = '';
    public string $HTTP_HOST = '';
    public string $HTTP_REFERER = '';
    public string $HTTP_USER_AGENT = '';
    public string $PATH = '';
    public string $PHP_SELF = '';
    public string $QUERY_STRING = '';
    public string $REDIRECT_HTTPS = '';
    public string $REDIRECT_QUERY_STRING = '';
    public string $REDIRECT_REDIRECT_HTTPS = '';
    public string $REDIRECT_REDIRECT_HTTP_AUTHORIZATION = '';
    public string $REDIRECT_REDIRECT_REDIRECT_HTTPS = '';
    public string $REDIRECT_REDIRECT_REDIRECT_SSL_TLS_SNI = '';
    public int $REDIRECT_REDIRECT_REDIRECT_STATUS = 0;
    public string $REDIRECT_REDIRECT_REDIRECT_UNIQUE_ID = '';
    public string $REDIRECT_REDIRECT_SSL_TLS_SNI = '';
    public int $REDIRECT_REDIRECT_STATUS = 0;
    public string $REDIRECT_REDIRECT_UNIQUE_ID = '';
    public string $REDIRECT_SSL_CIPHER = '';
    public int $REDIRECT_SSL_CIPHER_ALGKEYSIZE = 0;
    public string $REDIRECT_SSL_CIPHER_EXPORT = '';
    public int $REDIRECT_SSL_CIPHER_USEKEYSIZE = 0;
    public string $REDIRECT_SSL_CLIENT_VERIFY = '';
    public string $REDIRECT_SSL_COMPRESS_METHOD = '';
    public string $REDIRECT_SSL_PROTOCOL = '';
    public string $REDIRECT_SSL_SECURE_RENEG = '';
    public string $REDIRECT_SSL_SERVER_A_KEY = '';
    public string $REDIRECT_SSL_SERVER_A_SIG = '';
    public string $REDIRECT_SSL_SERVER_I_DN = '';
    public string $REDIRECT_SSL_SERVER_I_DN_C = '';
    public string $REDIRECT_SSL_SERVER_I_DN_CN = '';
    public string $REDIRECT_SSL_SERVER_I_DN_L = '';
    public string $REDIRECT_SSL_SERVER_I_DN_O = '';
    public string $REDIRECT_SSL_SERVER_I_DN_ST = '';
    public int $REDIRECT_SSL_SERVER_M_SERIAL = 0;
    public int $REDIRECT_SSL_SERVER_M_VERSION = 0;
    public string $REDIRECT_SSL_SERVER_S_DN = '';
    public string $REDIRECT_SSL_SERVER_S_DN_C = '';
    public string $REDIRECT_SSL_SERVER_S_DN_CN = '';
    public string $REDIRECT_SSL_SERVER_S_DN_L = '';
    public string $REDIRECT_SSL_SERVER_S_DN_O = '';
    public string $REDIRECT_SSL_SERVER_S_DN_ST = '';
    public string $REDIRECT_SSL_SERVER_V_END = '';
    public string $REDIRECT_SSL_SERVER_V_START = '';
    public string $REDIRECT_SSL_SESSION_ID = '';
    public string $REDIRECT_SSL_SESSION_RESUMED = '';
    public string $REDIRECT_SSL_TLS_SNI = '';
    public string $REDIRECT_SSL_VERSION_INTERFACE = '';
    public string $REDIRECT_SSL_VERSION_LIBRARY = '';
    public int $REDIRECT_STATUS = 0;
    public string $REDIRECT_UNIQUE_ID = '';
    public string $REDIRECT_URL = '';
    public string $REMOTE_ADDR = '';
    public int $REMOTE_PORT = 0;
    public string $REQUEST_METHOD = '';
    public string $REQUEST_SCHEME = '';
    public int $REQUEST_TIME = 0;
    public float $REQUEST_TIME_FLOAT = 0.0;
    public string $REQUEST_URI = '';
    public string $SCRIPT_FILENAME = '';
    public string $SCRIPT_NAME = '';
    public string $SERVER_ADDR = '';
    public string $SERVER_ADMIN = '';
    public string $SERVER_NAME = '';
    public int $SERVER_PORT = 0;
    public string $SERVER_PROTOCOL = '';
    public string $SERVER_SIGNATURE = '';
    public string $SERVER_SOFTWARE = '';
    public string $SSL_CIPHER = '';
    public int $SSL_CIPHER_ALGKEYSIZE = 0;
    public string $SSL_CIPHER_EXPORT = '';
    public int $SSL_CIPHER_USEKEYSIZE = 0;
    public string $SSL_CLIENT_VERIFY = '';
    public string $SSL_COMPRESS_METHOD = '';
    public string $SSL_PROTOCOL = '';
    public string $SSL_SECURE_RENEG = '';
    public string $SSL_SERVER_A_KEY = '';
    public string $SSL_SERVER_A_SIG = '';
    public string $SSL_SERVER_I_DN = '';
    public string $SSL_SERVER_I_DN_C = '';
    public string $SSL_SERVER_I_DN_CN = '';
    public string $SSL_SERVER_I_DN_L = '';
    public string $SSL_SERVER_I_DN_O = '';
    public string $SSL_SERVER_I_DN_ST = '';
    public int $SSL_SERVER_M_SERIAL = 0;
    public int $SSL_SERVER_M_VERSION = 0;
    public string $SSL_SERVER_S_DN = '';
    public string $SSL_SERVER_S_DN_C = '';
    public string $SSL_SERVER_S_DN_CN = '';
    public string $SSL_SERVER_S_DN_L = '';
    public string $SSL_SERVER_S_DN_O = '';
    public string $SSL_SERVER_S_DN_ST = '';
    public string $SSL_SERVER_V_END = '';
    public string $SSL_SERVER_V_START = '';
    public string $SSL_SESSION_ID = '';
    public string $SSL_SESSION_RESUMED = '';
    public string $SSL_TLS_SNI = '';
    public string $SSL_VERSION_INTERFACE = '';
    public string $SSL_VERSION_LIBRARY = '';
    public string $UNIQUE_ID = '';
    /**
     *	@description	
     *	@param	
     */
    protected function beforeConstruct($array)
    {
        return $_SERVER;
    }
}