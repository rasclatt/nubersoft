<?php 
if(!function_exists("autoload_function")) return;
autoload_function('default_jQuery,FetchPublicPage'); ?><!DOCTYPE html>
<head>
<meta charset="utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<title>Forbidden</title>
<link href='http<?php echo (empty($_SERVER['HTTPS']))? "":"s"; ?>://fonts.googleapis.com/css?family=Zeyada' rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="SHORTCUT ICON" HREF="/favicon.png">
<link rel="stylesheet" href="/css/menu.css" />
<link rel="stylesheet" href="/css/default.css" />
<link rel="stylesheet" href="/css/contrabrand.css" />
<?php echo default_jQuery(true); ?>
<style>
p, h1 {
	text-shadow: 1px 1px 2px #FFFFFF;
}
.cursive p {
	font-family: 'Zeyada', cursive;
	text-align: center;
	font-size: 20px;
}
h1 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	text-align: center;
}
.allblocks	{
	padding: 40px 0;
	border-top: 5px solid #C00;
	border-bottom: 5px solid #C5AB8C;
	text-align: center;
}
.allblocks h1,
.allblocks p	{
	width: 100%;
	text-align: center;
}
.wrapper	{
	background-color: #FFF;
	margin: 60px auto;
	width: 100%;
	display: inline-block;
}
body	{
	background-color: #EBEBEB;
}
</style>
</head>
<body>
<div class="wrapper">
	<div class="allblocks">
		<h1>Forbidden</h1>
		<p>This page is forbidden.</p>
		<a class="nbrbackbttn" href="<?php $backbutton = FetchPublicPage(); echo $backbutton['url']; ?>"><?php echo $backbutton['name']; ?></a>
	</div>
</div>
</body>
</html>