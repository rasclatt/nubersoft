<?php
namespace Nubersoft;

if((defined('FTP_REMOTE_ACTIVE') && !FTP_REMOTE_ACTIVE) || !defined('FTP_REMOTE_ACTIVE'))
	return;

# Default settings for okayed extensions
$def_types		=	array('jpg','jpeg','gif','png','php','html','xml','css');
# Fetch from registry
$reg_types		=	$this->getMatchedArray(['ftp','extensions','type'],$this->getRegistry());
# Assign if not empty
if(!empty($reg_types['type'][0]))
   $def_types	=	$reg_types['type'][0];
# Get defines
$remote			=	(defined("FTP_REMOTE_HOST"))? FTP_REMOTE_HOST : false;
$start			=	(defined("FTP_REMOTE_DIR"))? FTP_REMOTE_DIR : false;
$user			=	(defined("FTP_REMOTE_USERNAME"))? FTP_REMOTE_USERNAME : false;
$local_path		=	(defined("FTP_REMOTE_LOCAL_PATH"))? FTP_REMOTE_LOCAL_PATH : false;
$remote_path	=	(defined("FTP_REMOTE_REMOTE_PATH"))? FTP_REMOTE_REMOTE_PATH : false;
$port			=	(defined("FTP_REMOTE_PORT"))? FTP_REMOTE_PORT : 21;
$timeout		=	(defined("FTP_REMOTE_TIMEOUT"))? FTP_REMOTE_TIMEOUT : 90;

if($this->getPost('action') == 'nbr_sync_server') {
	$user			=	$this->getPost('ftp_username');
	$pass			=	$this->getPost('ftp_password');
	$local_path		=	$this->toSingleDs(DS.$this->getPost('local_path').DS);
	$remote_path	=	$this->toSingleDs(DS.$this->getPost('remote_path').DS);
	$remote			=	$this->getPost('host');
	$port			=	$this->getPost('port');
	$FTP			=	new nFtp($remote,$user,$pass,$remote_path,$port,$timeout);
	$client			=	$FTP->dirList();
	$basedir		=	pathinfo($remote_path,PATHINFO_BASENAME);
	$filtered		=	false;
	if(is_array($client)) {
		$filtered	=	array_filter(array_map(function($v) use ($basedir){
			$path	=	array_filter(explode('/',$v));
			return (in_array($basedir,$path));
		
		},$client));
	}
	
	if(!empty($filtered)) {
		$movelist		=	$FTP->recurseDownload($client,$local_path,$def_types)->getList();
		
		if(empty($this->getPost('check_host'))) {
			if(!empty($movelist['to'])) {
				foreach($movelist['to'] as $key => $path) {
					if($this->isDir(pathinfo($path,PATHINFO_DIRNAME)))
						$FTP->doWhile($movelist['from'][$key],$path,function($from,$to) {
					});
				}
			}
		}
	}
	else {
		$this->toMsgAdminAlert('Path doesn\'t match! No "'.$basedir.'" found.');
	}
	$FTP->close();
}
?>
<div class="admintools_dashboard_subsec">
	<h3 class="nbr_ux_element nTrigger nbr_pointer" data-instructions='{"FX":{"fx":["slideUp","slideToggle"],"acton":[".hideall","next::accordian"],"fxspeed":["fast","fast"]}}'>Remote Sync</h3>
	<div class="hideall" style="display: none; font-family: Arial, Helvetica, sans-serif;">
		<p>Retrieve files from the remote server</p>
		<?php
		$thisObj	=	$this;
		if(!empty($movelist)) { ?>
			<div style="padding: 10px; background-color: #A1AFA9; border: 2px dotted #333;">
			<?php
			if(!empty($movelist['from']))
				echo '<h4 class="nbr_ux_element" style="margin: 0;">FROM</h4><p style="font-size: 11px; font-family: Courier; color: #222;">'.implode('<br />',$movelist['from']).'</p>';
			if(!empty($movelist['to']))
				echo '<h4 class="nbr_ux_element" style="margin: 0;">TO</h4><p style="font-size: 11px; font-family: Courier; color: #222;">'.implode('<br />',array_map(function($v) use ($thisObj){
					return $thisObj->stripRoot($v);
				},$movelist['to'])).'</p>';
			?>
			</div>
			<?php
		}
		?>
		
		<form action="" method="post">
			<input type="hidden" name="action" value="nbr_sync_server" />
			<hr />
			<label style="font-size: 12px;">HOST</label>
			<input type="text" name="host" value="<?php echo $remote ?>" autocomplete="off" style="font-size: 16px; width: 60%;" />
			<label style="font-size: 12px;">PORT</label>
			<input type="text" name="port" value="<?php echo $port ?>" autocomplete="off" style="font-size: 16px;" size="4" />
			<hr />
			<label style="font-size: 12px;">REMOTE</label>
			<input type="text" name="remote_path" value="<?php echo $remote_path ?>" autocomplete="off" style="font-size: 16px; width: 99%;" />
			<hr />
			<label style="font-size: 12px;">LOCAL&nbsp;&nbsp;&nbsp;</label>
			<input type="text" name="local_path" value="<?php echo $local_path ?>" autocomplete="off" style="font-size: 16px; width: 99%;" />
			<hr />
			<label style="font-size: 12px;">USERNAME</label>
			<input type="text" name="ftp_username" value="<?php echo $user ?>" placeholder="Username" style="font-size: 16px; width: 99%;" autocomplete="off" />
			<hr />
			<label style="font-size: 12px;">PASSWORD</label>
			<input type="password" name="ftp_password" value="" autocomplete="off" style="font-size: 16px; width: 99%;" />
			<label style="font-size: 12px;"><input type="checkbox" name="check_host" />Check connection</label>
			<div class="nbr_button" style="float: right;">
				<input type="submit" value="FETCH" />
			</div>
		</form>
	</div>
	<?php
	$msg	=	$this->msgAdminAlert();
	if(!empty($msg)) {
		foreach($msg as $message)
			echo '<div class="nbr_error">'.$message.'</div>';
	}
	?>
</div>
<script>
$(document).ready(function() {
	$('input[name=ftp_username], input[name=ftp_password]').on('focus',function(e) {
		$(this).val((e.target.type == 'text')? '<?php echo $user ?>' : '');
	});
});
</script>