<?php
namespace Nubersoft\Localization;

use \Nubersoft\{
    nApp,
    nRouter as Router,
    Localization\View as Locale,
    Localization\Controller,
    nSession,
    nObserver,
    Settings\enMasse as SettingsTrait,
    nQuery\enMasse as nQueryTrait
};

/**
 * @description 
 */
class Observer extends nSession implements nObserver
{
    use SettingsTrait;
    use nQueryTrait;

    private $Localization, $nApp, $request;
    /**
     * @description 
     */
    public function __construct(
        Controller $Controller,
        nApp $nApp
    ) {
        $this->nApp = $nApp;
        $this->request = $this->nApp->getPost();
        $this->Localization = $Controller;
    }
    /**
     * @description 
     */
    public function actionListen()
    {
        $POST = $this->request;
        $action = (isset($POST['subaction'])) ? $POST['subaction'] : $POST['action'];
        $respto = (!empty($POST['sendto'])) ? \Nubersoft\Conversion\Data::getBoolVal($POST['sendto']) : false;
        $locale = $this->nApp->getSession('locale');

        switch ($action) {
            case ('create_translator'):
                if (!empty($POST['deliver']['formData'])) {
                    $POST = $this->nApp->getHelper('Conversion\Data')->arrayFromQueryString($POST['deliver']['formData']);
                }
                $lang = strtolower($this->nApp->getCookie('language'));
                if ($lang != 'en') {
                    $translation  = $this->Localization->saveTranslation(...[
                        "{$POST['transkey']}us{$lang}",
                        $this->nApp->dec($POST['description']),
                        ($POST['category_id']) ?? 'translator',
                        ($POST['ref_page']) ?? null
                    ]);
                }

                $this->nApp->ajaxResponse([
                    'alert' => (!empty($translation)) ? $this->nApp->getHelper('ErrorMessaging')->getMessageAuto('success_saved') : $this->nApp->getHelper('ErrorMessaging')->getMessageAuto('fail_saved')
                ]);
        }
    }
    /**
     * @description 
     */
    public function listen()
    {
        $redirect = array_filter([
            $this->setLocalization('locale', 'country'),
            $this->setLocalization('locale_lang', 'language', 'en')
        ]);

        if (!empty($redirect)) {
            $this->nApp->redirect($this->getRedirectingPath());
        }
    }
    /**
     * @description 
     */
    private function setLocalization($sess_tag, $req_tag, $def = 'us')
    {
        $Session  = new \Nubersoft\nSession();
        $req  = $this->nApp->getGet($req_tag);
        # If empty or is being set
        if (empty($this->nApp->getSession($sess_tag)) || $req) {
            # If being set
            if ($req) {
                # Get the abbr
                $req  = substr(strtolower($req), 0, 2);
                # See if this is a valid language
                $isActive = $this->Localization->localeAttrActive('language', $req);
                # Check if user is admin
                $isAdmin  = $this->nApp->isAdmin();
                # If active, change the language
                if ($isActive) {
                    if (($isActive == 'adm' && $isAdmin) || $isActive == 'on') {
                        $Session->destroy($sess_tag);
                        $Session->set($sess_tag, $req);
                    }
                    $redirect = true;
                }
            }
            # If not being set, set automatically
            else {
                $Session->set($sess_tag, $def);
            }
        }

        return (isset($redirect));
    }
    /**
     * @description 
     */
    private function getRedirectingPath()
    {
        $parse = parse_url($this->getDataNode('_SERVER')['REQUEST_URI']);
        if (!isset($parse['query']))
            return $this->getDataNode('_SERVER')['REQUEST_URI'];
        $arr  = [];
        parse_str($this->nApp->dec($parse['query']), $arr);

        if (isset($arr['country']))
            unset($arr['country']);

        if (isset($arr['language']))
            unset($arr['language']);

        $query  = http_build_query($arr);
        return $parse['path'] . ((empty($query)) ? '' : '?' . $query);
    }
    /**
     * @description 
     */
    public function updateLocaleSettings()
    {
        $Settings = $this->getSettingsModel();
        $filters = [
            'country',
            'language'
        ];
        # Check if the countries key is available
        $csv  = (!empty($this->nApp->getFiles()[0]->countries)) ? $this->nApp->getFiles()[0] : null;
        # See if this is a CSV file
        if ($csv->type != 'text/csv')
            $csv = false;
        # Set to array
        $csv = $csv->toArray();
        # Assign the post
        $POST = $this->request;
        # Fetch out the countires and such
        if (!empty($csv['tmp_name'])) {
            $countries  = array_filter(array_map(function ($v) {
                return $v[0];
            }, array_map('str_getcsv', file($csv['tmp_name']))));
            $POST['country']  = [];
            $i = 1;
            foreach ($countries as $abbr) {
                $POST['country']['name'][]  = $abbr;
                $POST['country']['page_order'][]  = $i;
                $POST['country']['page_live'][]  = 'on';
                $i++;
            }
        }

        foreach ($filters as $filter) {
            if (!empty($Settings->getOption($filter, 'locale')))
                $Settings->deleteOption($filter, 'locale');

            if (!empty($POST[$filter]['name'][0])) {
                foreach ($POST[$filter]['name'] as $k => $v) {

                    if (empty($v))
                        continue;

                    $this->query("INSERT INTO `system_settings` (`option_group_name`,`category_id`,`option_attribute`,`page_order`,`page_live`) VALUES ('locale', ?, ?, ?, ?)", [
                        $filter,
                        strtolower($v),
                        $POST[$filter]['page_order'][$k],
                        $POST[$filter]['page_live'][$k]
                    ]);
                }
            }
        }

        $this->nApp->toSuccess('Locale settings saved.');

        return $this;
    }
    /**
     * @description 
     */
    public function toggleEditMode()
    {
        if (!$this->nApp->isAdmin())
            return false;
        $Session = $this->nApp->getHelper('nSession');
        $Session->destroy('translator_mode');
        if ($this->nApp->getGet('subaction') == 'on')
            $Session->set('translator_mode', $this->nApp->getGet('subaction'));
        $path = $this->getDataNode('routing_info')['path'];
        $path = (empty($path)) ? '/' : "/{$path}/";
        $this->nApp->redirect($path);
    }
    /**
     * @description 
     */
    public function apiListener()
    {
        # Hide all errors
        //$this->reportErrors();
        # Stop of no translation requested
        if ($this->nApp->getPost('service') != 'translation')
            return $this;
        # Check if ajax requiest
        $ajax = $this->nApp->isAjaxRequest();
        # Stop of there are not requested keys
        if (empty($this->nApp->getPost('keys')) && $this->nApp->getPost('subservice') != 'store') {
            $class = ($ajax) ? '\Exception' : '\Nubersoft\Exception\Ajax';
            throw new $class('Keys are required for translating.', 200);
        }
        try {
            $filter  = $this->getSystemOption('transhost');
            $host = "{$this->nApp->getHost('domain')}.{$this->nApp->getHost('tld')}";

            if (empty($filter))
                return $this;

            if (!is_array($filter))
                $filter = [$filter];
            $referrer  = $this->nApp->getServer('HTTP_REFERER');
            $refHost  = explode('.', parse_url($referrer)['host']);
            $refHostComb = array_pop($refHost);
            $refHostComb = strtolower(array_pop($refHost) . '.' . $refHostComb);
            $allow  = false;
            foreach ($filter as $h) {
                if ($refHostComb == strtolower($h))
                    $allow  = true;
            }
            if (!$allow)
                return $this;
        } catch (\Exception $e) {
            throw new \Nubersoft\Exception\Ajax($e->getMessage(), 500);
        }

        try {
            $referrer = Router::createRoutingData($this->nApp->getServer('HTTP_REFERER'));
            $domain = "{$referrer['domain']}.{$referrer['tld']}";
            # Create keys
            if ($this->nApp->getPost('generate')) {
                $Locale = new Locale($this->nApp->getPost('lang'), 'us');
                foreach ($this->nApp->getPost('generate') as $key => $value) {
                    if (!$Locale->transKeyExists($key)) {
                        $value  = $this->nApp->dec($value);
                        $Locale->saveTransKey($key, $value, 'auto');

                        foreach ($this->Localization->getActiveLanguages() as $activeLang) {
                            $l  = strtolower($activeLang['option_attribute']);
                            if ($l == 'en')
                                continue;

                            $this->Localization->saveTranslation("{$key}us{$l}", $value);
                        }
                    }
                }
                $this->nApp->ajaxResponse([
                    'msg' => 'Keys run',
                    'success' => 1,
                    'referrer' => $domain,
                    'keys' => $this->nApp->getPost('generate')
                ]);
            }
            # Store all the keys to find translations for
            $keys = array_map(function ($v) {
                return "{$v}us{$this->nApp->getPost('lang')}";
            }, $this->nApp->getPost('keys'));
            # Combine language-specific and general keys
            $keys = array_merge($keys, $this->nApp->getPost('keys'));
            # See if any keys are availavble
            $kcount = count($keys);
            if ($kcount > 0) {
                # Search
                $translations = $this->query("SELECT `title`, `content`, `component_type` FROM components WHERE title IN (" . implode(',', array_fill(0, $kcount, '?')) . ")", $keys)->getResults();
            }
            # Assemble translation response
            if (!empty($translations)) {
                foreach ($translations as $row) {
                    $language = ($row['component_type'] == 'transkey') ? 'en' : $this->nApp->getPost('lang');
                    $tk = ($language != 'en') ? substr($row['title'], 0, -4) : $row['title'];
                    $new[$language]['trans-cls'][$tk] = $this->nApp->dec($row['content']);
                }
            }
            # Save a default reply
            if (empty($new))
                $new[$this->nApp->getPost('lang')]['trans-cls'] = [];

            $this->nApp->ajaxResponse($new);
        } catch (\PDOException $e) {
            $this->nApp->ajaxResponse([
                'message' =>  $e->getMessage() . 'Invalid request',
                'success' => false
            ]);
        } catch (\Exception $e) {
            $this->nApp->ajaxResponse([
                'message' =>  $e->getMessage(),
                'success' => false
            ]);
        }
    }
    /**
     * @description 
     */
    public function autoGenTranslations()
    {
        $co = ($this->nApp->getPost('co')) ? $this->nApp->getPost('co') : 'us';
        $languages = $this->Localization->getActiveLanguages();
        $allKeys  = \Nubersoft\ArrayWorks::organizeByKey($this->query("SELECT title as t, content from components WHERE component_type = 'transkey'")->getResults(), 't');
        $stored = false;

        foreach ($languages as $key => $value) {
            if ($value['option_attribute'] == 'en')
                continue;
            foreach ($allKeys as $transkey_value => $content) {
                $tkey = "{$transkey_value}us{$value['option_attribute']}";
                if (!$this->Localization->translationExists($tkey)) {
                    $this->Localization->saveTranslation($tkey, $content['content']);
                    $stored = true;
                }
            }
        }
        if ($stored)
            $this->nApp->toSuccess("Translation keys have been generated.");
        else
            $this->nApp->toSuccess("All translation keys are up to date.");
    }
    /**
     * @description 
     */
    public function createTransHost()
    {
        $this->query("DELETE FROM system_settings WHERE category_id = 'transhost'");
        $hosts  = array_filter($this->nApp->getPost('option_attribute'));
        if (empty($hosts)) {
            $this->nApp->toSuccess("Translation whitelisted hosts update.");
            return $this;
        }
        foreach ($hosts as $host) {
            $this->query("INSERT INTO system_settings (`category_id`,`option_group_name`,`option_attribute`,`page_live`) VALUES ('transhost','system',?,'on')", [$host]);
        }
        $this->nApp->toSuccess("Translation whitelisted hosts update.");
        return $this;
    }
}
