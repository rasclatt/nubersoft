<?php
$title	=	$this->getHtml('title');

if(!empty($this->getPageURI('page_options')->page->title))
	$title	=	$this->getPageURI('page_options')->page->title;
?>
<!DOCTYPE html>
<html>
<title><?php echo $title ?></title>
<head profile="http://www.w3.org/2005/10/profile">
<?php echo $this->getHtml('meta') ?>
<?php echo $this->getViewPort() ?>
<?php echo $this->getHtml('favicons').PHP_EOL ?>
<?php if(is_file(NBR_ROOT_DIR.DS.'admintools.svg')) { ?>
<link rel="mask-icon" href="/admintools.svg" color="red">
<?php } ?>
<?php if(is_file(NBR_ROOT_DIR.DS.'favicon.png')) { ?>
<link rel="apple-touch-icon image_src" href="/favicon.png">
<?php } ?>
<?php if(is_file(NBR_ROOT_DIR.DS.'favicon.ico')) { ?>
<link rel="shortcut icon" href="/favicon.ico">
<?php } ?>
<?php echo $this->getMediaSrc('javascript') ?>
<?php echo $this->getMediaSrc('stylesheet') ?>
<?php
if(!empty($this->getHtml('javascript'))) {
?>
<script>
<?php echo $this->getHtml('javascript') ?>
</script>
<?php
}
?>
<?php echo $this->getTemplateDoc('noscript.php').PHP_EOL; ?>
</head>