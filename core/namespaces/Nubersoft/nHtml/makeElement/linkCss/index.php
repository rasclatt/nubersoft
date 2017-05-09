<?php
$link	=	site_url().str_replace(DS,"/",$this->stripRoot($useData['path']));
?>
<link type="text/css" rel="stylesheet" href="<?php echo $link; ?>?v=<?php echo date("ymdhis",filemtime($useData['path'])); ?>" />