<?php
$DS	=	DIRECTORY_SEPARATOR;
require_once(__DIR__.$DS.'..'.$DS.'..'.$DS.'config.php');

use Nubersoft\nApp as nApp;

if(!is_admin())
	return;
		
	$nubquery	=	nquery();
	
	if((isset($_POST['plugin_action']) && $_POST['plugin_action'] == 'dbdl')) {

		if(!isset($_POST['table'])) {
			header("Location: ".$_SERVER['HTTP_REFERER']);
			exit;
		}

		if(!empty($_POST['table'])) {

			if(!isset(nApp::$settings->engine->temp_folder))
				nApp::$settings->engine->temp_folder	=	ROOT_DIR.'/../temp/';

			$csv	=	new ZipEngine(nApp::$settings->engine->temp_folder);

			foreach($_POST['table'] as $name) {
				$query	=	$nubquery->addCustom("describe ".$name,true)->getResults();

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
				$tables		=	nApp::call()->toArray(nApp::call()->getTables());
				
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