<?php
# Set defines
define('NBR_ROOT_DIR',__DIR__);
define('DS',DIRECTORY_SEPARATOR);
$archive	=	__DIR__.DS.'Archive.zip';
$zipper		=	new ZipArchive();
$zipper->open($archive);
$zipper->extractTo(NBR_ROOT_DIR);
$zipper->close();
# Remove the zip file
unlink($archive);