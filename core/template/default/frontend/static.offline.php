<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Temporarily down for maintenance</title>
<style>
html {
	background-color: #000;
	color: #FFF;
	font-family: Arial, Helvetica, sans-serif;
	height: 100%;
	width: 100%;
	padding: 0;
	margin: 0;
	display: table-cell;
	height: 100%;
	width: 100%;
	top: 0;
	bottom: 0;
	vertical-align: middle !important;
	text-align: center !important;
}
h1 {
	font-size: 35px;
}
p	{
	font-size: 20px;
}
.dialogue {
	display: inline-block;
	padding: 30px;
	border: 1px solid #666;
	margin: auto;
	background-color: #222;
	border-radius: 3px;
}
</style>
</head>
<body>
	<div class="dialogue">
		<h1>Site is currently under maintenance</h1>
		<p>We are currently working on <?php echo $_SERVER['HTTP_HOST'] ?>. We should be back shortly.</p>
	</div>
</body>
</html>
<script>
<?php echo printpre($this->getDataNode()) ?>