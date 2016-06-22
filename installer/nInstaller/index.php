<?php
// MasterMySQL
include("dbconnect.root.php");
$core	=	NuberEngine::getRegistry("onload");
// Create base core engine
(!$core->getAttr("onload/replace_core")->getAppStatus())? NuberEngine::Init()->Core() : $core->getApp();