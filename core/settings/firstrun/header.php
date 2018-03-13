<?php
$this->setErrorMode(1);
$this->autoload('printpre');
$root_path	=	(strpos($_SERVER['SCRIPT_URL'],'index.php') !== false)? '/'.trim(pathinfo($_SERVER['SCRIPT_URL'],PATHINFO_DIRNAME),'/') : rtrim($_SERVER['SCRIPT_URL'],'/');
?>
<!DOCTYPE html>
<html>
<title>Install</title>
<head profile="http://www.w3.org/2005/10/profile">
<meta name="Author" content="Rasclatt">
<meta name="charset" content="utf-8">
<meta name="Description" content="Simple Framework for PHP">
<meta name="Keywords" content="php,framework,jquery,javascript">
<meta name="viewport" content="width=device-width">
<script type="text/javascript" src="https://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript" src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo $root_path ?>/media/js/nFunctions.js?v=161206025821"></script>
<script type="text/javascript" src="<?php echo $root_path ?>/media/js/nScripts.js?v=161206104813"></script>
<script type="text/javascript" src="<?php echo $root_path ?>/media/js/helpers.js?v=161118111345"></script>
<script type="text/javascript" src="<?php echo $root_path ?>/core/namespaces/nPlugins/Nubersoft/ComponentTab/js/codetool.js?v=161109034908"></script>
<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Abel" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/media/css/grid.css?v=161130103839" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/media/css/form.css?v=161130103839" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/core/template/default/css/styles.css?v=161114010807" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/core/template/default/css/frontend/styles.css?v=161109104746" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/core/template/default/css/menu.css?v=161128113404" />
<link type="text/css" rel="stylesheet" href="<?php echo $root_path ?>/media/css/styles.css?v=161206050227" />

<style>
	#form-wrapper{
		padding: 2em;
		background-color: #CCC !important;
		text-align: center;
	}
</style>
</head><!-- START BODY -->
<body class="nbr" style="background-color: #000;">
<div id="loadspot_modal"></div>
<div id="content" class="nbr_wrapper col-count-3 offset">
	<div id="maincontent" class="col-2 push-col-3 large">