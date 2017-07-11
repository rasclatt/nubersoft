<?php
if(isset($settings['alt'])) {
	$alt	=	$settings['alt'];
	unset($settings['alt']);
}
else
	$alt	=	' ';
?>
<img src="<?php echo $url; if($version) { ?>?v=<?php echo $version; } ?>" alt="<?php echo $alt ?>" <?php echo implode(' ',$settings) ?> />