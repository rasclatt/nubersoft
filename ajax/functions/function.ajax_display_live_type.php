<?php
function ajax_display_live_type()
	{
		ob_start();
		AutoloadFunction('code_markup');
		echo '<pre style="background-color: transparent; width: 100%; max-width: 1000px; max-height: 500px; padding: 10px;">';
		echo code_markup(Safe::decode($_POST['content']),true);
		echo '</pre>';
?>
<style>
.te-brackets,
.te-ie,
.te-arr-wrp,
.te-arr,
.te-par,
.te-quote,
.te-str,
.te-semic,
.te-tag	{
	font-family: inherit;
}

.te-brackets	{
	color: blue;
}
.te-markup	{
	background-color: transparent;
	margin-bottom: 3px;
	display: inline-block;
	font-family: Courier;
	font-size: 14px;
	line-height: 14px;
	padding: 0;
	width: auto;
	min-width: none;
}
.te-phptag {
	color: red;
}
.te-ie	{
	color: green;
}
.te-arr-wrp	{
	color: #3366FF;
}
.te-arr	{
	color: #666;
}
.te-par	{
	color: #000;
}
.te-quote,
.te-tag	{
	color: orange;
}
.te-str	{
	color: blue;
}
.te-semic	{
	color: #900;
}
.te-const	{
	color: #C63;
}
</style>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}