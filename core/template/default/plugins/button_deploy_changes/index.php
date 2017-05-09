<?php
$scan	=	$this->getDirList(NBR_CLIENT_SETTINGS.DS.'deploy');
$count	=	(!empty($scan['host']))? count($scan['host']) : 0;
?>
<a href="<?php echo ($count == 0)? '#' : $this->adminUrl('/?requestTable='.$this->getGet('requestTable').'&action=nbr_deploy_changes') ?>" class="nbr_admintool_plugin_button"<?php if($count == 0) echo ' title="No packages available to deploy"' ?>><img src="<?php echo $this->getHelper('nImage')->toBase64(__DIR__.DS.'images'.DS.'button.jpg') ?>" /><?php if($count > 0) { ?><div style="font-size: 13px; padding: 3px; border-radius: 5px; background-color: red; position: relative; display: inline-block; margin: -10px; left: -5px; top: -20px; color: #FFF; text-decoration: none; min-width: 15px; font-weight: bold;"><?php echo $count ?></div><?php } ?></a>