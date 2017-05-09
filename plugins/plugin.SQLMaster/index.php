<?php
	if(is_file($sFile = __DIR__.'/../../config.php'))
		include_once($sFile);
	
	if(!is_admin())
		return;
	
	AutoloadFunction('nQuery');
	$nubquery	=	nQuery();
	
	if(check_empty($_POST,'plugin_action','sqlmaster')) {
			
			$statement	=	trim($_POST['sql']);
			
			if(!empty($statement)) {			
					if($_POST['query_type'] == 'w') {
							$nubquery->addCustom(Safe::decode(Safe::decode($statement)),true)->write();
						}
					else {
							$vals	=	$nubquery->addCustom(Safe::decode(Safe::decode($_POST['sql'])))->fetch(); ?>
					<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.8);" class="close-sql-window-wrap">
							<div id="sqlblock" style="max-width: 1100px; bottom: 0; position: relative; overflow: auto; background-color: #222;" class="close-sql-window">
							<?php printpre($vals); ?>
							</div>
					</div>
					
					<script>
						$(".close-sql-window").click(function() {
								$(".close-sql-window-wrap").fadeToggle(200);
							});
					</script>
					<?php
						}
				}
		} ?>
	<div id="sqlmaster-wrap">
		<div id="sqlmaster-click"></div>
		<div style="position: absolute; z-index: 1;">
			<div class="sqlmaster-drop">
			<form method="post" action="" id="sqlmaster-plugin">
				<div style="padding: 10px;">
					<select name="query_type">
						<option value="s">SELECT QUERY</option>
						<option value="w">WRITE QUERY</option>
					</select>
				</div>
				<div style="padding: 10px;">
					<textarea name="sql" placeholder="Write MySQL" style="font-size: 18px; padding: 10px;"></textarea>
				</div>
				<div style="text-align: center; padding: 10px 0;">
					<div class="formButton" style="margin: 0 auto; float: none; clear: none; display: inline-block;">
						<input disabled="disabled" type="submit" value="QUERY" id="save-reload" />
					</div>
				</div>
				<input type="hidden" name="plugin_action" value="sqlmaster" />
			</form>
			</div>
		</div>
	</div>
<script>
	$("#sqlmaster-click").click(function() {
			$(".sqlmaster-drop").slideToggle(200);
		});
</script>