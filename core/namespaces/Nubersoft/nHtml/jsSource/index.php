<?php
$localUrl	=	(!empty($useData['site_url']))? $useData['site_url'] : '';
$path		=	(!empty($useData['path']))? $useData['path'] : false;
$js			=	(!empty($useData['links']))? $useData['links'] : false;
$longPath	=	(!empty($useData['longPath']))? $useData['longPath'] : false;

if(empty($js) || empty($path) || empty($longPath))
	return false;

?>
<script type="text/javascript" src="<?php echo $localUrl.$path; ?>?v=<?php echo date("ymdhis",filemtime($longPath)); ?>"></script>