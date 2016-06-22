<?php
/*
**	@description	Will render the javascript block based on the return from the database site prefs
*/
function render_javascript($payload = false,$wrap = true)
	{
		if(!empty(nApp::getJavascript()) || !empty($payload)) {
			ob_start();
?>
<script>
<?php
if($wrap) {
echo nApp::jsEngine()->getHandle(); ?>(document).ready(function(ne) {
<?php
}
echo (!empty(nApp::getJavascript()))? nApp::getJavascript().PHP_EOL : "";
echo (!empty($payload))? $payload.PHP_EOL : "";
if($wrap) {
?>
});
<?php
}
?>
</script>
<?php		$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}
	}