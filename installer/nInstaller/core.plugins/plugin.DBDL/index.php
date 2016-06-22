<?php
$dir[]	=	__DIR__.'/../../dbconnect.root.php';
$dir[]	=	'dbconnect.root.php';

if(is_file($dir[0]))
	include_once($dir[0]);
elseif(is_file($dir[1]))
	include_once($dir[1]);

if(!function_exists('is_admin'))
	return;

if(!is_admin())
	return;
		
	$nubquery	=	nQuery();
	
	if((isset($_POST['plugin_action']) && $_POST['plugin_action'] == 'dbdl')) {
			
			if(!isset($_POST['table'])) {
					header("Location: ".$_SERVER['HTTP_REFERER']);
					exit;
				}
			
			if(!empty($_POST['table'])) {
					
					if(!isset(NubeData::$settings->engine->temp_folder))
						NubeData::$settings->engine->temp_folder	=	ROOT_DIR.'/../temp/';
						
					$csv	=	new ZipEngine(NubeData::$settings->engine->temp_folder);
					
					foreach($_POST['table'] as $name) {
							$query	=	$nubquery->addCustom("describe ".$name,true)->fetch();
							
							if($query != 0) {
									foreach($query as $row) {
											$search[]	=	$row['Field'];
										}
								}
							
							$csv->FetchTable($name,$search,$name);
							unset($search);
						}
					
					if(!isset($_POST['zip_name']))
						$zipname	=	date("YmdHis").preg_replace('/[^0-9a-zA-Z]/',"",$_SESSION['username']);
					else
						$zipname	=	date("YmdHis")."-".preg_replace('/[^0-9a-zA-Z]/',"",$_POST['zip_name']);
		
					$csv->Zipit($zipname.'.zip');
				}
			else
				$error	=	true;
		}
?>
	<div id="dbdl-wrap">
		<div id="dbdl-click"></div>
		<div id="plugin_DBDL_cont">
			<div class="dbdl-drop">
			<form method="post" action="/core.plugins/plugin.DBDL/index.php" id="dbdl-plugin">
				<div style="padding: 10px;">
				<input type="text" name="zip_name" placeholder="File Name" maxlength="10" style="font-size: 16px;" />.zip
				</div>
				<div id="plugin_DBDL_saveBtn">
					<div class="formButton plugin_DBDL_inputMod"><input disabled="disabled" type="submit" value="SAVE TO FILE" id="save-reload" /></div>
				</div>
				<input type="hidden" name="plugin_action" value="dbdl" />
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