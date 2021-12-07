<?php
namespace Nubersoft\nRouter;

use \Nubersoft\ {
    nObserver,
    nRouter\Controller as nRouterController
};

class Observer extends nRouterController implements nObserver
{

    public function listen()
    {
        $this->checkLastActive();
        $this->splitRequest();
    }
    /**
     * @description Checks the last activity in the session and destroys old session if past timeout
     */
    public function checkLastActive()
    {
        # Fetch session obj
        $Session = $this->getHelper('nSession');
        # Get the current active time
        $time = $Session->get('LAST_ACTIVE');
        # If there is none, it hasn't been set yet, stop
        if (empty($time))
            return false;
        # If the current time is past the timeout
        if ($time < strtotime('now')) {
            # Destroy session
            $Session->destroy();
            # If the user is actually logged in
            if ($this->isLoggedIn()) {
                # Notify the user (request dependent)
                if (!$this->isAjaxRequest())
                    $this->redirect($this->localeUrl($this->getPage('full_path')));
                else {
                    # Fetch the uri of the current page
                    $current = $this->getHelper('nCookie')->get('nbr_current_page');
                    # Redirect path
                    $path = (!empty($current['request'])) ? $current['request'] : '/';
                    # Respond
                    $this->ajaxResponse([
                        'alert' => 'Your session has expired.',
                        'html' => [
                            '<script>window.location = "' . $path . '?action=logout";</script>'
                        ],
                        'sendto' => [
                            '#loadspot-modal'
                        ]
                    ]);
                }
            }
        }
    }
    /**
     *	@description	
     */
    public function splitRequest()
    {
        if (empty($this->getServer())) {
            trigger_error('You must have the DataNode conversion process active.');
            return false;
        }

        $host = explode('.', $this->getServer('HTTP_HOST'));
        $host = [
            'ssl' => $this->getServer('HTTPS') == 'on',
            'ajax' => $this->isAjaxRequest(),
            'host' => $this->getServer('HTTP_HOST'),
            'subdomain' => (count($host) > 2) ? array_shift($host) : '',
            'tld' => array_pop($host),
            'domain' => implode($host),
        ];
        $arr = [];
        parse_str($this->dec($this->getServer('QUERY_STRING')), $arr);

        if (!empty($arr)) {
            if (preg_match('!/$!', key($arr))) {
                $host['path'] = trim(key($arr), '/');
                array_shift($arr);
            }
        }
        if (empty($host['path']))
            $host['path'] = '';

        $host['locale'] = $this->getSession('locale');
        $host['locale_lang'] = $this->getSession('locale_lang');
        $route_split = array_merge($host, ['query' => $arr]);
        $this->getHelper('DataNode')->setNode('routing_info', $route_split);
    }
}
