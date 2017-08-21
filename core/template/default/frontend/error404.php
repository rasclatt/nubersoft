<?php
$message	=	$this->toArray($this->getDataNode('error404'));
$title		=	(isset($message['title']))? strip_tags($message['title']) : "Whoops! Wrong Page.";
$image		=	$this->getHelper('nImage')->toBase64(__DIR__.DS.'error404'.DS.'background.jpg');
?>
<style>
p, h1 {
	text-shadow: 1px 1px 2px #000;
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
	border-top: 5px solid #FFF;
	border-bottom: 5px solid #FFF;
	background-color: transparent !important;
}
.allblocks h1,
.allblocks p	{
	width: 100%;
	text-align: center;
}
.wrapper	{
	background-color: rgba(0,0,0,0.6);
	margin: 60px auto;
	width: 100%;
	display: inline-block;
	color: #FFF;
}
body	{
	padding: 0;
	margin: 0;
}
.wrapper	{
	text-align: center;
}
.allblocks	{
	margin: 0 auto;
	text-align: center;
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
	background-color: rgba(255,255,255,0.6);
	background: linear-gradient(#EBEBEB,#888);
	text-shadow: 1px 1px 2px #FFF;
	text-align: center;
	box-shadow: 1px 1px 6px rgba(0,0,0,0.5);
}
<?php if($image) { ?>
html {
	background: url(<?php echo $image; ?>) no-repeat center center fixed;
	background-size: cover;
	vertical-align: middle;
}<?php } ?>

p	{
	font-size: 20px !important;
	line-height: 26px;
}
h1 {
	font-size: 35px !important;
	text-transform: uppercase;
}
</style>
<script>
$(document).ready(function() {
	$("select").change(function() {
		var Value	=	$(this).val();
	
		if(Value)
			window.location = Value;
	});
});
</script>
<div class="wrapper">
	<div class="allblocks">
		<div style="max-width: 500px; margin: 30px auto; padding: 0 20px;">
			<h1><?php echo $this->getHelper('Safe')->decode($title) ?></h1>
			<p><?php echo (isset($message['body']))? $message['body'] : "This page does not exist or has moved!"; ?></p>
		</div>
<?php
# If the current page is not real
if(!$this->getPage('ID')) {
	$getGet			=	trim($this->getPageURI('invalid_uri'),'/');
	
	if(empty($getGet))
		return;
	# Function suggests pages based on key words derived from request string
	$suggestions	=	$this->getFunction('suggest_public_page',explode('/',$getGet));
	if(!empty($suggestions)) {
?>			<h3>Try <?php $getcnt = (count($suggestions) > 1); echo ($getcnt)? "these":"this"; ?> suggestion<?php echo ($getcnt)? "s":""; ?>:</h3>
			<div class="nbr_select">
				<select>
					<option value="">Select Page</option>
<?php	foreach($suggestions as $menu_title => $set) {
			if($set['page_live'] != 'on')
				continue;
?>					<option value="<?php echo  (!empty($set['full_path']))? $set['full_path'] : $set['full_path']; ?>"><?php echo $menu_title; ?></option>
<?php 	}
?>				</select>
			</div>
<?php	
	}
}
?>	</div>
</div>