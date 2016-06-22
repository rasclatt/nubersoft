<?php
include_once(__DIR__.'/../dbconnect.root.php');
AutoloadFunction("ajax_edit_component",__DIR__.'/functions/');
echo ajax_edit_component();