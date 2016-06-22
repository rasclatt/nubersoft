<?php if(!function_exists("AutoloadFunction")) return; ?><!DOCTYPE html>
<head>
<meta charset="utf-8" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">
<title>Error!</title>
<link href='http<?php echo (empty($_SERVER['HTTPS']))? "":"s"; ?>://fonts.googleapis.com/css?family=Zeyada' rel='stylesheet' type='text/css'>
<?php AutoloadFunction('default_jQuery'); echo default_jQuery(false); ?>
<style>
p, h1 {
	text-shadow: 1px 1px 2px #FFFFFF;
	font-family: Arial, Helvetica, sans-serif;
}
.cursive p {
	font-family: 'Zeyada', cursive;
	text-align: center;
	font-size: 20px;
}
h1,h2,h3 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
	text-align: center;
}
.allblocks	{
	box-shadow: inset 1px 1px 20px rgba(0,0,0,0.6);
	padding: 40px 0;
	border-top: 5px solid #C00;
	border-bottom: 5px solid #C5AB8C;
}
.allblocks h1,
.allblocks p	{
	width: 100%;
	text-align: center;
}
.wrapper	{
	background-color: #CCC;
	margin: 60px auto;
	width: 100%;
	display: inline-block;
}
body	{
	background-color: #333;
	padding: 0;
	margin: 0;
}
.wrapper	{
	text-align: center;
}
.allblocks	{
	margin: 0 auto;
}
.nbr_select select	{
	-webkit-appearance: none;
	appearance: none;
	font-size: 18px;
	font-family: Arial, Helvetica, sans-serif;
	border: 5px solid #C00;
	border-top: none;
	border-bottom: none;
	border-radius: 3px;
	padding: 10px 15px;
	color: #222;
	background-color: #EBEBEB;
	background: linear-gradient(#EBEBEB,#888);
	text-shadow: 1px 1px 2px #FFF;
	text-align: center;
	box-shadow: 1px 1px 6px rgba(0,0,0,0.5);
}
</style>
</head>
<body>

<div class="wrapper">
	<div class="allblocks">
		<h1><?php echo (isset($message['title']))? $message['title'] : "Whoops! Wrong Page."; ?></h1>
		<p><?php echo (isset($message['body']))? $message['body'] : "This page does not exist or has moved!"; ?></p>
		<?php
			// Function suggests pages based on key words derived from request string
			AutoloadFunction("SuggestPublicPage");
			NubeData::$settings->_GET	=	(!empty(NubeData::$settings->_GET))? NubeData::$settings->_GET:array();
			$suggestions	=	SuggestPublicPage(array_keys((array) NubeData::$settings->_GET));
			if(!empty($suggestions)) { ?>
			<h3>Try <?php $getcnt = (count($suggestions) > 1); echo ($getcnt)? "these":"this"; ?> suggestion<?php echo ($getcnt)? "s":""; ?>:</h3>
			<div class="nbr_select">
				<select>
					<option value="">Select Page</option>
				<?php
					foreach($suggestions as $set) { ?>
					<option value="<?php echo  (!empty($set[0]['full_path']))? $set[0]['full_path'] : $set['full_path']; ?>"><?php echo (!empty($set[0]['menu_name']))? $set[0]['menu_name']: $set['menu_name']; ?></option>
					<?php } ?>
				</select>
			</div>
			<script>
			$("select").change(function() {
				var Value	=	$(this).val();
	
				if(Value)
					window.location = Value;
			});
			</script>
		<?php }
		?>
	</div>
</div>
</body>
</html>