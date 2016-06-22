<?php
if(!function_exists('is_admin'))
	return;
elseif(!is_admin())
	return;
?>	<div id="dbdl-wrap">
		<div id="dbdl-click"></div>
		<div id="plugin_DBDL_cont">
			<div class="dbdl-drop">
			<form method="post" action="" id="dbdl-plugin">
				<input type="hidden" name="token[nProcessor]" value="<?php echo nApp::nToken()->getSetToken('nProcessor',array('DBDL',rand(1000,9999)),true); ?>" />
				<div style="padding: 10px;">
				<input type="text" name="zip_name" placeholder="File Name" maxlength="10" style="font-size: 16px;" />.zip
				</div>
				<div id="plugin_DBDL_saveBtn">
					<div class="formButton plugin_DBDL_inputMod"><input disabled="disabled" type="submit" value="SAVE TO FILE" id="save-reload" /></div>
				</div>
				<input type="hidden" name="action" value="nbr_db_dl" />
				<div id="plugin_DBDL_dropwrap">
					<table>
				<?php
				$_db_creds	=	new FetchCreds();
				$mysqlTable	=	base64_decode($_db_creds->_creds['data']);
				$tables		=	Safe::to_array(nApp::getTables());
				
				foreach($tables as $table) {
?>						<tr>
							<td style="padding: 5px 10px;">
							<input type="checkbox" name="table[]" value="<?php echo $table; ?>" id="<?php echo $table; ?>" />
							<label for="<?php echo $table; ?>"><span style="font-size: 14px;"><?php echo ucwords(str_replace("_"," ",$table)); ?></span></label>
							</td>
						</tr>
			<?php	} ?>
					</table>
				</div>
			</form>
			</div>
		</div>
	</div>