<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Error: <?php echo $type; ?></title>
<link href='https://fonts.googleapis.com/css?family=Abel' rel='stylesheet' type='text/css'>
<style>
.nFont	{
	font-family: 'Abel', sans-serif !important;
}
a:link,
a:visited	{
	text-decoration: none;
}
code	{
	background-color: #FFF;
	padding: 1px 5px;
	border: 1px solid #888;
	border-radius: 3px;
}
p	{
	line-height: 20px;
}
code.fullwidth	{
	display: block;
	padding: 0 !important;
	overflow: auto;
}
pre	{
	padding: 0;
	margin: 0;
}
span.errorplot	{
	color: red;
}
</style>
<?php
autoload_function('render_element_css,render_element_js,default_jQuery');
echo default_jQuery();
echo render_element_js(NBR_ROOT_DIR.'/js/',false);
echo render_element_css(NBR_ROOT_DIR.'/css/',false);
?>
</head>
<body class="nbr" style="background-color: #524648;">