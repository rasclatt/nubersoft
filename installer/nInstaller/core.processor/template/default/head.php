<?php if(!function_exists("AutoloadFunction")) return;
AutoloadFunction("get_page_title,render_meta");
?><!DOCTYPE html>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width">
<title><?php echo get_page_title(); ?></title>
<?php if($prefs['head']) {
?>
<head profile="http://www.w3.org/2005/10/profile">
<?php echo render_header($prefs).PHP_EOL; ?>
<?php echo render_meta().PHP_EOL; ?>
<?php if(!empty($elements)) echo Safe::decode($elements).PHP_EOL; ?>
<?php if(is_admin()) { ?>
<script src="/js/admintools.js"></script>
<link type="text/css" rel="stylesheet" href="/css/admintools.css"/>
<link type="text/css" rel="stylesheet" href="/css/components.css"/>
<?php } ?>
</head>
<?php }
?>