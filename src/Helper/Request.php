<?php
namespace Nubersoft\Helper;

use \Nubersoft\Dto\Server;

use \Nubersoft\Dto\Helper\{
    Request\GetRequest,
    Request\GetInputResponse,
    ArrayWorks\ToObjectRequest
};

class Request
{
    protected static $raw_request;
    private $request, $get, $post, $put, $delete, $patch, $session, $cookie;
    /**
     * @description 
     */
    public function __construct(Server $server)
    {
        $fetch = self::getInput($server);
        $this->get = $this->filter($_GET);
        $this->post = $this->filter($_POST);
        $this->request = $this->filter($_REQUEST);
        $this->put = ($fetch->request_type == 'put') ? $this->filter($fetch->request) : [];
        $this->delete = ($fetch->request_type == 'delete') ? $this->filter($fetch->request) : [];
        $this->patch = ($fetch->request_type == 'patch') ? $this->filter($fetch->request) : [];
    }
    /**
     * @description Trims the input data recursively
     */
    private function filter($array): ?array
    {
        if (!is_array($array))
            return trim($array);
        $new = [];
        foreach ($array as $key => $value) {
            $new[$key] = (is_array($value)) ? $this->filter($value) : trim($value);
        }

        return $new;
    }
    /**
     *	@description	Creates and stores the request
     */
    public static function getInput(Server $server): GetInputResponse
    {
        if (empty(self::$raw_request)) {
            $parse = null;
            $request = file_get_contents('php://input');
            $request_method = $server->REQUEST_METHOD;

            if (!empty($request) && is_string($request)) {
                $dto = new \Nubersoft\Dto\Helper\ArrayWorks\ToObjectRequest();
                $dto->mixed = $request;
                $parse = ArrayWorks::toObject($dto);
                if (!is_array($parse)) {
                    $arr = [];
                    parse_str((string) self::$raw_request, $arr);
                    if (!empty($arr))
                        $parse = $arr;
                }
            }
            self::$raw_request = new GetInputResponse([
                'request' => $parse,
                'request_type' => strtolower($request_method)
            ]);
        }

        return self::$raw_request;
    }
    /**
     *	@description	Returns request
     */
    public static function get(GetRequest $request)
    {
        switch ($request->type) {
            case ('get'):
                $REQ = $_GET;
                break;
            case ('request'):
                $REQ = $_REQUEST;
                break;
            case ('files'):
                $REQ = $_FILES;
                break;
            case ('put'):
                $REQ = $_POST;
                break;
            case ('delete'):
                $REQ = $_POST;
                break;
            case ('server'):
                $REQ = $_SERVER;
                break;
            default:
                $REQ = $_POST;
        }

        if (!empty($request->key))
            return (isset($REQ[$request->key])) ? $REQ[$request->key] : null;

        $dto = new ToObjectRequest;
        $dto->mixed = $REQ;
        $dto->to_array = !$request->to_object;
        return ($request->to_object) ? ArrayWorks::toObject($dto) : $REQ;
    }
    /**
     * @description 
     */
    public function __call($method, $args = false)
    {
        $method = strtolower(preg_replace('/^get/', '', $method));
        $use = $this->{$method};

        if (!empty($args[0]))
            return ($use[$args[0]]) ?? null;

        return $use;
    }
}
