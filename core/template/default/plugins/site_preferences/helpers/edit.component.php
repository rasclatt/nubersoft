<?php
include_once(__DIR__.'/../config.php');
autoload_function("ajax_edit_component",__DIR__.'/functions/');
echo ajax_edit_component();