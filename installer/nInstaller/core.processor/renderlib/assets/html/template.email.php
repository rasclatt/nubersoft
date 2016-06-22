<?php if(!isset($message)) return; ?>
<html>
<title>Online Submission Form</title>
<head>
<link rel="stylesheet" href="http://~SERVER::[HTTP_HOST]~/css/contrabrand.css" type="text/css" />
<link rel="stylesheet" href="http://~SERVER::[HTTP_HOST]~/css/default.css" type="text/css" />
<style>
body {
	background-color: #EBEBEB;
}
#wrapper {
	width:800px;
	margin: 3% auto 60px auto;
}
.head_container	{
	background-image: url(http://~SERVER::[HTTP_HOST]~/core.processor/images/email/background.jpg);
	background-repeat: no-repeat;
	display: block;
	width: 780px;
	height: 136px;
	padding: 10px;
	border-top-left-radius: 6px;
	border-top-right-radius: 6px;
}
.l-hand-size	{
	width: 380px;
	display: inline-block;
	float: left;
	clear: none;
}
p.header-h1	{
	display: inline-block;
	color: #FFFFFF;
	text-shadow: 2px 2px 2px #333333;
	font-size: 26px;
	float: left;
	clear: none;
	margin: 0;
	padding: 10px;
}
a.login-button:link,
a.login-button:visited	{
	display: inline-block;
	float: left;
	clear: left;
	margin: 10;
}
.r-hand-size	{
	width: 380px;
	display: inline-block;
	float: right;
	clear: none;
	text-align: right;
}
img.default-logo	{
	display: block;
	float: right;
	max-width: 200px;
	max-height: 100px;
}
.body-content	{
	width: 738px;
	display: inline-block;
	float: left;
	clear: left;
	padding: 30px;
	border-left: 1px solid #888888;
	border-right: 1px solid #888888;
	border-bottom: 1px solid #888888;
	border-radius-bottom: 15px;
	margin-bottom: 30px;
	background-color: #FFFFFF;
	border-bottom-left-radius: 6px;
	border-bottom-right-radius: 6px;
}
</style>
</head>
<body>
<div id="wrapper">
	<div class="head_container">
		<div class="l-hand-side">
			<a class="formLinkButton login-button" href="http://~SERVER::[HTTP_HOST]~">Login</a>
		</div>
		<div class="r-hand-size">
			<img src="http://~SERVER::[HTTP_HOST]~/client_assets/images/logo/default.png" class="default-logo" />
		</div>
	</div>
	<div class="body-content">
		<h2><?php echo $subject; ?></h2>
		<p><?php echo $message; ?></p>
	</div>
</div>
</body>
</html>