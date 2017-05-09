<?php
/*
**	This page will change the template to use the reset password page by altering the use_page attribute.
**	In addition to this, it will inform the page not to cache because the page
**	is not the normal view for this page so caching is not a good idea
*/
// Load page
$page	=	'include.resetpassword.php';
// No cache
$cache	=	false;
// Reset the page data so the page will load normally through the application
$opts	=	array(
				'page_prefs'=>array(
					'use_page'=>$page,
					'auto_cache'=>$cache
					),
				'pageURI'=>array(
					'use_page'=>$page,
					'auto_cache'=>$cache
					)
				);
// Reset the data
\Nubersoft\nApp::call()->resetDataNodeVals($opts);