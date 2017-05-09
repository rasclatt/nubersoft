<!DOCTYPE html>
<html>
<title><?php echo $this->getHtml('title') ?></title>
<head profile="http://www.w3.org/2005/10/profile">
<?php echo $this->getHtml('meta') ?>
<?php echo $this->getViewPort() ?>
<?php echo $this->getHtml('favicons').PHP_EOL ?>
<?php echo $this->getMediaSrc('javascript') ?>
<?php echo $this->getMediaSrc('stylesheet');
if(!empty($this->getHtml('javascript'))) {
?>
<script>
<?php echo $this->getHtml('javascript') ?>
</script>
<?php
}
?>
<?php echo $this->getDoc('noscript.php').PHP_EOL; ?>
</head>