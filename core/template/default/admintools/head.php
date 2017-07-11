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