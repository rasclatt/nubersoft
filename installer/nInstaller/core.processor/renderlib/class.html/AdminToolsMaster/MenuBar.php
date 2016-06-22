
<div style="text-align: center; padding:0; background-color: #333;">
	<div id="install_menu" style="margin: 0 auto; float: none; display: inline-block; width: auto;">
		<div><a href="?reinstall=<?php echo rtrim($token_reinstall."&".create_query_string(array("reinstall","command"),$_GET),"&"); ?>">Update / Install.</a></div>
		<div><a href="?reinstall=<?php echo rtrim($token_reinstall."&".create_query_string(array("reinstall","command"),$_GET),"&"); ?>">Set up databases.</a></div>
		<div><a href="?page_editor=true<?php echo rtrim("&".create_query_string(array("load_page"),$_GET,true),"&"); ?>">Page Editor</a></div>
	</div>
</div>