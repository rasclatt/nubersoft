<?php
include($this->getBackEnd(DS.'inclusions'.DS.'config.admin.php'));

$nAdminTools		=	new \Nubersoft\nAdminTools(
							new \Nubersoft\configFunctions(
								new \Nubersoft\nAutomator($this)
							)
						);

$nAdminTools	->useConfigs(NBR_CLIENT_DIR);
if(!$this->onWhiteList($_SERVER['REMOTE_ADDR']))
	die('You must be on the whitelist to view this page.');

$component	=	$this->getGet('cId');
$sql		=	"select `ID`,`unique_id`,`content` from `components` where `ID` = :0";
$this->useData['ID']	=	$this->nQuery()->query($sql,array($component))->getResults();
$this->saveSetting('pageURI',array('title'=>$this->useData['ID'][0]['unique_id']));
echo $this->getHeader();
?>
</head>