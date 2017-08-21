<?php
if(!empty($this->getPage()->full_path))
# Include template settings
include(__DIR__.DS.'inclusions'.DS.'config.php');
$title	=	(!empty($this->getDataNode('error404')->title))? $this->getDataNode('error404')->title : 'Error';
# Start a cache engine
$cache	=	$this->getHelper('nCache');
# If there is a header file, just get it. If not, make it
$cache->cacheBegin($this->getCacheFolder(DS.'template'.DS.md5($title).DS.'error404_header.html'));
# If there is no header file, render contents of the header
# (everything between braces will be stored in cache)
if(!$cache->isCached()) {
?><!DOCTYPE html>
<html>
<title><?php echo $this->safe()->decode($title); ?></title>
<head profile="http://www.w3.org/2005/10/profile">
<?php echo $this->getHtml('favicons') ?>
<?php echo $this->getMediaSrc('javascript') ?>
<?php echo $this->getMediaSrc('stylesheet') ?>
</head>
<?php
}
# Stop the caching, save the contents into a file, render the block
# This portion is all irrelavent if file exists
# (for instance ->getCached() will be empty)
echo $cache->cacheRender();
