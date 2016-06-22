<?php
include_once(__DIR__.'/../dbconnect.root.php');
// Close the writing to session
// All writing should be closed
session_write_close();
AutoloadFunction("ajax_delete_cache",__DIR__.'/functions/');
echo ajax_delete_cache();