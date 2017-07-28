<?php
include_once(__DIR__.'/../config.php');
// Close the writing to session
// All writing should be closed
session_write_close();
autoload_function("ajax_delete_cache",__DIR__.'/functions/');
echo ajax_delete_cache();