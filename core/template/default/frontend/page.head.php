<?php
# Include template settings
if(!empty($this->getPage()->full_path))
	include(__DIR__.DS.'inclusions'.DS.'config.php');
# Create a cache of the header else always render
if(!$this->isAdmin()) {
	$Cache	=	$this->getHelper('nCache');
	$Cache->cacheBegin($this->setCachePath('header.html'));
	if(!$Cache->isCached())
		# Render the cached file
		echo $this->getHeader();
	# Write from render or from file	
	echo $Cache->cacheRender();
}
else
	# This is the raw header
	echo $this->getHeader();