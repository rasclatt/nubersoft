<?php
/*Title: use_markup()*/
/*Description: This function is the main function that matches all text markup.*/
/*Example: `~app::TestIsBest[setting="value"]~` */
use Nubersoft\nApp as nApp;

function use_markup($string = false)
{
    if(!empty($string)) {
        $nApp    =    nApp::call();
        if(!is_array($string)) {
            $nApp->autoload('apply_markup');

            $val    =    preg_replace_callback('/(\~[^\~]{1,}\~)/i','apply_markup',$string);

            return $val;
        }
        else {
            $nApp->autoload(array('printpre'));
            if($nApp->isAdmin()) {
                return _('Input can not be a dataset (array).').printpre($string,__LINE__);
            }
        }
    }
}