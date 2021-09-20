<?php
namespace Nubersoft\nRouter;

use \Nubersoft\{
    Conversion\Data as Conversion,
    JWTFactory as JWT
};

class Controller extends \Nubersoft\nRouter
{
    private $Conversion;
    /**
     *	@description	
     */
    public function __construct(Conversion $Conversion)
    {
        $this->Conversion = $Conversion;
    }

    public function setHeader(): Controller
    {
        $args = func_get_args();
        $exit = (isset($args[1])) ? $args[1] : false;
        if (!is_array($args[0]))
            $args[0]  =  [$args[0]];

        foreach ($args[0] as $header) {
            header($header);
        }

        if ($exit)
            exit;

        return $this;
    }
    /**
     *	@description	Set xss protection if the user is not an admin logged in
     */
    public function detectAdminXss(): Controller
    {
        header('X-XSS-Protection: ' . ((!$this->isAdmin()) ? '1; mode=block' : '0'));
        return $this;
    }
    /**
     * @description Standard redirect
     */
    public function redirect(string $location): void
    {
        $JWT = JWT::get();
        # Fetch overrides for router
        $reg = $this->Conversion->xmlToArray(NBR_CLIENT_DIR . DS . 'settings' . DS . 'core' . DS . 'router.xml');
        # Fetch the redirect action
        $action =   ($reg['redirect']) ?? false;
        # If not empty try new router
        if (!empty($action)) {
            # Run that router
            \Nubersoft\nReflect::instantiate($action)->redirect($location);
        }
        # Parse the url to process it's query
        $arr  =  parse_url($location);
        # Process query
        if (!empty($arr['query'])) {
            parse_str($arr['query'], $arr['query']);
            if (isset($arr['query']['msg'])) {
                # First see if the message is already encoded
                try {
                    $decode =   $JWT->get($arr['query']['msg']);
                } catch (\Exception $e) {
                    # If not encoded, create a new one
                    $arr['query']['msg']  =  $JWT->create([
                        'expire' => time() + 5,
                        'msg' => $arr['query']['msg']
                    ]);
                }
                # Rebuild redirect
                $arr['query']  =  '?' . http_build_query($arr['query']);
                if (!isset($arr['scheme']))
                    $arr['scheme']  =   '';
                $arr['scheme']    .=    ':';

                $location  =  implode('', $arr);
            }
        }
        # Redirect
        $this->setHeader("Location: {$location}", true);
    }
    /**
     * @description Checks if the locale request is ajax-based request
     */
    public function isAjaxRequest(): bool
    {
        $type = $this->getDataNode('request');
        if (!empty($type))
            return ($type == 'ajax');
        else
            return (strtolower($this->getServer('HTTP_X_REQUESTED_WITH')) == 'xmlhttprequest');
    }
    /**
     * @description Ajax-based response
     */
    public function ajaxResponse($item, $modal = false, bool $header = true)
    {
        if ($modal) {
            if (!empty($item['html'][0])) {
                $item['html'][0] = \Nubersoft\nApp::createContainer(function (\Nubersoft\Plugin $Plugin) use ($item) {
                    return $Plugin->setPluginContent('modal', [
                        'html' => $item['html'][0],
                        'title' => (!empty($item['title'])) ? $item['title'] : ""
                    ])->getPlugin('modal_window');
                });
                $this->jsonResponse($item, $header);
            }
        } else {
            if (is_array($item) || is_object($item))
                $this->jsonResponse($item, $header);
            else
                die($item);
        }
    }
    /**
     *	@description	Respond with Json
     */
    private function jsonResponse($data, bool $header)
    {
        if ($header)
            header('Content-Type: application/json');
        
        die(json_encode($data));
    }
}
