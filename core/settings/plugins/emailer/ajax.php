<?php

if(!$this->isAdmin())
	$this->ajaxResponse(array('alert'=>'You are not an admin user or not logged in.'));
	
$Emailer	=	$this->getPlugin('\Emailer\Component');
$emails		=	$Emailer->getAllEmailReceipts(1,100);
$ids		=	(is_array($emails))? array_map(function($v){ return $v['ID']; },$emails) : array();
$emails		=	$Emailer->fetchColumns($emails,array('to','email_from','ip','timestamp','header','subject','raw_message'));
$count		=	(is_array($ids))? count($ids) : '';
ob_start();
if($count >= 1) {
	echo PHP_EOL;
?>
<div style="padding: 5px; text-align: center; font-family: Arial, Helvetica, sans-serif; font-weight: bold; border-radius: 15px; color: #FFF; min-width: 16px; background-color: red; position: relative; left: -10px; margin: -20px; display: inline-block; box-shadow: 0 0 10px rgba(0,0,0,0.6);"><?php echo $count ?></div>
<?php
}
$data	=	ob_get_contents();
ob_end_clean();
$this->ajaxResponse(array('sendto'=>array('.nbr_new_emails'),'html'=>array($data)));