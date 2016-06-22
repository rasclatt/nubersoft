<?php
include_once(__DIR__.'/../config.php');
include_once(__DIR__.'/function.helpStep.php');

if(!is_admin())
	die('<h2>You must be logged in as an administrator</h2>');
	
$creds	=	normalizeCreds($creds);

try
	{
		$file = @fetchInstaller();
		if(!is_file($file))
			throw new Exception("<h2>An error occurred. File did not download.</h2>");
	}
catch(Exception $e)
	{
		die($e->getMessage());
	}

echo 'Download Installer...<br />';
// Unzip files to this spot
$tempFDir	=	__DIR__.'/../nInstaller/';
// Unzip database to this spot
$tempDDir	=	__DIR__.'/../nDatabase/';
// Copy unzipped files to this spot
$instFDir	=	__DIR__."/../../";
// Unzip files
unZip($file,$tempFDir);
// Move the files
moveFiles($tempFDir,$instFDir);

if(!empty($_SESSION['install_instruct']['purge'])) {
	// Download the database
	$db	= fetchInstaller(array("link"=>'http://www.nubersoft.com/client_assets/installer/nubersoft_sql.zip'));
	if($db) {
		echo 'Download Database...<br />';
		unZip($db,$tempDDir);
		if(is_file($sqlFile = str_replace("//","/",$tempDDir."/nubersoft.sql"))) {
			echo 'Decompressed Database, Installing...<br />';
			foreach($creds['api'] as $key => $value) {
				$creds['api'][str_replace("n_","",$key)]	=	Safe::decOpenSSL($value,$_SESSION['install_key']);
			}
			
			foreach($creds['db'] as $key => $value) {
				$creds['database'][$key]	=	Safe::decOpenSSL($value,$_SESSION['install_key']);
			}

			saveCredentials($creds);
			
			$con	=	false;
			isValidConnect($creds['database'],$con);
			
			if(!empty($con)) {
				$query	=	$con->query("show tables in {$creds['database']['database']}");

				while($row = $query->fetch(PDO::FETCH_ASSOC))
					$results[]	=	$row;
					$filter[]	=	'component_builder';
					$filter[]	=	'components';
					$filter[]	=	'dropdown_menus';
					$filter[]	=	'emailer';
					$filter[]	=	'file_types';
					$filter[]	=	'form_builder';
					$filter[]	=	'form_rules';
					$filter[]	=	'help_desk';
					$filter[]	=	'image_bucket';
					$filter[]	=	'main_menus';
					$filter[]	=	'members_connected';
					$filter[]	=	'menu_display';
					$filter[]	=	'routing_table';
					$filter[]	=	'system_settings';
					$filter[]	=	'upload_directory';
					$filter[]	=	'users';
					$tValid		=	array();
					if(!empty($results)) {
						AutoloadFunction("organize");
						$tables	=	array_keys(organize($results,'Tables_in_'.$creds['database']['database']));
						
						foreach($tables as $tCheck) {
							$tValid[]	=	(in_array($tCheck,$filter));
						}
					}
					
					$iUpdate	=	(array_sum($tValid) == count($filter));
							
					if(!$iUpdate) {
						$resp	=	$con->query(file_get_contents($sqlFile));
						echo printpre($resp,'query');		
				}
			}
		}
	}
}

$fastFunc	=	function($val) {
		$path	=	pathinfo($val);
		return (!empty($path['dirname']))? ($path['dirname']) : false;
	};

foreach($creds['db'] as $key => $value) {
	$creds['database'][$key]	=	Safe::decOpenSSL($value,$_SESSION['install_key']);
}

saveCredentials($creds);

clean_up_install(array($tempDDir,$tempFDir,$fastFunc($file),$fastFunc($db),NBR_ROOT_DIR.'/setup/'));


unset($_SESSION['install']);
?>		<div class="left-just">
			<input type="hidden" name="action" value="get_step" />
			<h1>Installation</h1>
		</div>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="3" value="BACK" /></div>
			</li>
			<li>
<?php
	
	if(!empty($creds['database'])) {
			foreach($creds['database'] as $key => $value) {
				$valid[substr($key,0,4)]	=	$value;
			}
		
			$menus	=	nQuery(DatabaseEngine::connect(array("creds"=>$valid)),true);
			$q		=	$menus	->select(array("full_path","menu_name"))
								->from("main_menus",true)
								->fetch();
								
			if($q != 0) {
				AutoloadFunction("site_url");
?>
			<label>Select page to jump to</label>
			<select id="jumper">
				<option value="">SELECT JUMP-TO</option>
<?php		if(is_file(__DIR__.'/../../.htaccess')) {	

			foreach($q as $links) {
				if(empty(trim($links['menu_name'])))
					continue;
					
?>				<option value="<?php echo site_url().$links['full_path']; ?>"><?php echo $links['menu_name']; ?></option>
<?php		}
			}
			else {
?>				<option value="<?php echo site_url(); ?>">HOME PAGE</option>
<?php		}
?>
			</select>
<?php	} ?>
				
<script>
$(document).ready(function() {
	$(".hidePostInstall").hide();
	$("#jumper").change(function(){
		var thisLink	=	$(this).val();
		if(thisLink != '')
			window.location=thisLink;
	});
});
</script>
<?php }
	else {
?>				<a class="nbr_button" href="/">FINISH</a>
<?php	}
?>			</li>
		</ul>