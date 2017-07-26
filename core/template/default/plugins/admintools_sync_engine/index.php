<?php
namespace Nubersoft;

if((defined('FTP_REMOTE_ACTIVE') && !FTP_REMOTE_ACTIVE) || !defined('FTP_REMOTE_ACTIVE'))
	return;

$remote			=	(defined("FTP_REMOTE_HOST"))? FTP_REMOTE_HOST : false;
$start			=	(defined("FTP_REMOTE_DIR"))? FTP_REMOTE_DIR : false;
$user			=	(defined("FTP_REMOTE_USERNAME"))? FTP_REMOTE_USERNAME : false;
$local_path		=	(defined("FTP_REMOTE_LOCAL_PATH"))? FTP_REMOTE_LOCAL_PATH : false;
$remote_path	=	(defined("FTP_REMOTE_REMOTE_PATH"))? FTP_REMOTE_REMOTE_PATH : false;
$port			=	(defined("FTP_REMOTE_PORT"))? FTP_REMOTE_PORT : 21;
$timeout		=	(defined("FTP_REMOTE_TIMEOUT"))? FTP_REMOTE_TIMEOUT : 90;

if($this->getPost('action') == 'nbr_sync_server') {
	$user			=	$this->getPost('username');
	$pass			=	$this->getPost('password');
	$local_path		=	$this->toSingleDs(DS.$this->getPost('local_path').DS);
	$remote_path	=	$this->toSingleDs(DS.$this->getPost('remote_path').DS);
	$remote			=	$this->getPost('host');
	$port			=	$this->getPost('port');
	$FTP			=	new nFtp($remote,$user,$pass,$remote_path,$port,$timeout);
	$client			=	$FTP->dirList();
	$movelist		=	$FTP->recurseDownload($client,$local_path)->getList();
	
	foreach($movelist['to'] as $key => $path) {
		if(!is_file($path)) {
			if($this->isDir(pathinfo($path,PATHINFO_DIRNAME)))
				$FTP->doWhile($movelist['from'][$key],$path,function($from,$to) {
			});
		}
	}
	$FTP->close();
}
?>
<div class="admintools_dashboard_subsec">
	<h3 class="nbr_ux_element nTrigger nbr_pointer" data-instructions='{"FX":{"fx":["slideUp","slideToggle"],"acton":[".hideall","next::accordian"],"fxspeed":["fast","fast"]}}'>Sync Live</h3>
	<div class="hideall" style="display: none; font-family: Arial, Helvetica, sans-serif;">
		<p>Retrieve files from the remote server</p>
		<form action="" method="post">
			<input type="hidden" name="action" value="nbr_sync_server" />
			<hr />
			<label style="font-size: 12px;">HOST</label>
			<input type="text" name="host" value="<?php echo $remote ?>" autocomplete="off" style="font-size: 16px; width: 60%;" />
			<label style="font-size: 12px;">PORT</label>
			<input type="text" name="port" value="<?php echo $port ?>" autocomplete="off" style="font-size: 16px;" size="4" />
			<hr />
			<label style="font-size: 12px;">LOCAL&nbsp;&nbsp;&nbsp;</label>
			<input type="text" name="local_path" value="<?php echo $local_path ?>" autocomplete="off" style="font-size: 16px; width: 99%;" />
			<hr />
			<label style="font-size: 12px;">USERNAME</label>
			<input type="username" name="username" value="<?php echo $user ?>" placeholder="Username" style="font-size: 16px; width: 99%;" />
			<hr />
			<label style="font-size: 12px;">PASSWORD</label>
			<input type="password" name="password" value="" style="font-size: 16px; width: 99%;" />
			<div class="nbr_button">
				<input type="submit" value="FETCH" />
			</div>
		</form>
	</div>
</div>