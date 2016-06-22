<?php
// Configuration
require("config.php");
// Master MySQL Initialize
CoreMySQL::Initialize();
// Load the workflow xml prefs
\Nubersoft\nObserverProcessor::createApp('default'); 
// Get registry
$core		=	NuberEngine::getRegistry("onload");
// Set an automator for reploading
$settings	=	array(
					'action_trigger'=>'preload', // name="preload" value="action_name"
					'request_type'=>'post' // Only excepts posts
				);
// Start an Automation observer
\nApp::nAutomator()->observer($settings);
// Create base core engine
if(!$core->getAttr("onload/replace_core")->getAppStatus())
	NuberEngine::init()->core();
else
	$core->getApp();