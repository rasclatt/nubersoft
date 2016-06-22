<?php
	class	coreInstaller
		{
			public		$first_run;
			public		$db_dir;
			
			protected	$nubquery;
			
			private		$classes_dir;
			private		$dbconnect_dir;
			private		$root_dir;
			private		$redirect;
			private		$timezone;
			private		$errors_status;
			
			public	function __construct($_creds = array())
				{
					register_use(__METHOD__);
					
					AutoloadFunction('is_admin,check_empty,nQuery');
					$this->nubquery	=	nQuery();
					$_classes		=	(isset($classes_dir) && !empty($classes_dir))? NBR_ROOT_DIR.$classes_dir: getcwd();
					$_database		=	(isset($dbconnect_dir) && !empty($dbconnect_dir))? NBR_ROOT_DIR.$dbconnect_dir: getcwd();
					$_root			=	(isset($root_dir) && !empty($root_dir))? NBR_ROOT_DIR.$root_dir: NBR_ROOT_DIR;
				
					// Needs to know where to get the classes
					$checkDB		=	new BuildDatabase($_creds);
				}
			
			public	function headerInstructions($timezone,$errors_status = 0, $redirect)
				{
					register_use(__METHOD__);
					
					$this->redirect			=	$redirect;
					$this->timezone			=	date_default_timezone_set($timezone);
					
					// Try connecting via ftp for password purposes
					if(check_empty($_POST,'action','run')) :
						
						$isdir				=	(is_dir($_REQUEST['install_location']))? true: false;
						
						if($isdir == true)
							$root_dir		=	$_REQUEST['install_location'];
						else {
								$root_dir	=	NBR_ROOT_DIR;
								$error		=	'This directory does not exist. Installing in root directory.';
							}
						
						// Local update folder
						$curr[1]	=	$root_dir . '/client_assets/';
						$curr[2]	=	$curr[1] . 'update/';
						
						// Destination log, local log, destination download, local download, toggle errors on
						$install	=	new updater($root_dir,'http://www.nubersoft.com/update/log.txt', $curr[2] . 'log.txt', 'http://www.nubersoft.com/update/', $curr[2], true);
						
					//	print_r($install->final_report);
						$_SESSION['usergroup']	=	'1';
						$_SESSION['username']	=	'Admin';
						$_SESSION['first_run']	=	true;
						header("Location: " . str_replace(NBR_ROOT_DIR, "", $curr_dir . '?layout=simple'));
						exit;
					endif;
					
					if($this->redirect	== true) :
						// Check to see if there is a db file
						if(is_file(NBR_ROOT_DIR.'/client_assets/settings/dbcreds.php')) {
								include('classes/timeout.php');
								if(!isset($_SESSION['usergroup']) || (!is_admin(2))) {
									header("Location: http://" . $_SERVER['HTTP_HOST']);
									exit;
								}
							}
						else
							{
								if(!isset($_SESSION['first_run'])) {
									$this->first_run		=	true;
									$_SESSION['usergroup']	=	'1';
									$_SESSION['username']	=	'Admin';
									$_SESSION['first_run']	=	true;
									header("Location: " . str_replace("index.php","",$_SERVER['PHP_SELF']));
									exit;
								}
							}
					
					else :
						if(!isset($_SESSION['first_run']) && (!is_file(NBR_ROOT_DIR . '/client_assets/settings/dbcreds.php'))) :
							$this->first_run	=	true;
						//	print_r($install->final_report);
							$_SESSION['usergroup']	=	'2';
							$_SESSION['username']	=	'Admin';
							$_SESSION['first_run']	=	true;
							header("Location: " . str_replace("index.php","",$_SERVER['PHP_SELF']));
							exit;
						endif;
					endif;					
					
					$all_dirs		=	NBR_ROOT_DIR.'/';
					$select_root	=	scandir($all_dirs);
				}
			
			public	function execute($first_run = false,$db_dir = '/core.processor/includes/')
				{
					register_use(__METHOD__);
					
					$this->first_run	=	$first_run;
					$this->db_dir		=	(isset($db_dir))? $db_dir: '';
					$select_root		=	scandir(NBR_ROOT_DIR);
					
					if((isset($_GET['inst']) && $_GET['inst'] == 1) && is_admin()) { ?>
	<div style="display: block; margin: 0 auto; min-width: 320px; width: 100%;"><?php
		if(is_admin()) : ?>
        <div style=" width: 94%; min-width: 320px; margin: 0 auto; padding: 30px 3%;">
            <div id="hide_install"><?php
						$my_version			=	new updater('','', '','','', false);
						
					//	$ns_log_file		=	new ArrayObject($my_version->process_log('http://www.nubersoft.com/update/log.txt'));
					//	$ns_log_file		=	explode("::",$ns_log_file[0]);
					//	$ns_curr_version	=	$ns_log_file[0];
					//	$ns_curr_date		=	$ns_log_file[1];
					//	$ns_curr_type		=	$ns_log_file[2];
						
						$my_local_log		=	NBR_ROOT_DIR . '/client_assets/update/log.txt';
						
						if(is_file($my_local_log)) {
							$my_log_file			=	new ArrayObject($my_version->process_log($my_local_log));
							$my_log_file			=	explode("::",$my_log_file[0]);
							$my_curr_version		=	(!empty($my_log_file[0]))? $my_log_file[0]: 'Unknown';
							$my_curr_date			=	(!empty($my_log_file[1]))? $my_log_file[1]: 'Unknown';
							$my_curr_type			=	(!empty($my_log_file[2]))? $my_log_file[2]: 'Unknown';
							
							$need_update			=	(isset($my_curr_date) && $my_curr_date < $ns_curr_date)? true: false;
							
							}
						else {
								$unknown			=	'<span style="color: red;">Unknown</span>';
								$need_update		=	true;
								$my_curr_type		=	$unknown;
								$my_curr_date		=	$unknown;
								$my_curr_version	=	$unknown;
							} ?>
		<div style="width: 100%; display: inline-block; margin: 30px 0;">
            <h2><?php if($need_update == true) echo '<img src="images/check_off.png" style="width: 30px; padding-right: 5px; position: relative; top: 5px;" /><span style="color: red;">Update available.</span>'; else echo '<span style="color: green;">Up to date.</span>'; ?></h2>
            <div style="width: 100%; margin: 20px 0 0 0; display: inline-block; max-width: 550px;">
                <table style="padding: 0 20px 0 0; float: left; width: 50%;">
                    <tr>
                        <td colspan="2"><h3>Current Version Info</h3></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Version:</p></td><td><p><?php echo str_replace("_", ".", $ns_curr_version); ?></p></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Date:</p></td><td><p><?php echo str_replace("_", ".", $ns_curr_date); ?></p></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Build:</p></td><td><p><?php echo str_replace("_", ".", $ns_curr_type); ?></p></td>
                    </tr>
                </table>
                <table style="padding: 0 20px 0 0; float: left; width: 50%;">
                    <tr>
                        <td colspan="2"><h3>My Version Info</h3></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Version:</p></td><td><p><?php echo str_replace("_", ".", $my_curr_version); ?></p></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Date:</p></td><td><p><?php echo str_replace("_", ".", $my_curr_date); ?></p></td>
                    </tr>
                    <tr>
                        <td style="width: 80px;"><p>Build:</p></td><td><?php echo $my_curr_type; ?></p></td>
                    </tr>
                </table>
            </div>
                <?php if(isset($_GET['action']) && $_GET['action'] == 'run') { ?>
                <table>
                    <tr>
                        <td>
                            <a class="base_button" href="DBQuickBuilder/" style="font-size: 16px; padding: 8px 16px;">DB Quick Builder Tool</a><br />
                        </td>
                        <td>
                                <a class="base_button" href="/core.processor/renderlib/admintools/" style="font-size: 16px; padding: 8px 16px;">Admin Tools</a>
                        </td>
                        <td>
                            <div onmousedown="MM_changeProp('thinker','','display','block','DIV')">
                                <a class="base_button" href="?action=run" style="font-size: 16px; padding: 8px 16px;">Check Build</a>
                            </div>
                        </td>
                    </tr>
                </table>
                <?php
                    }
                else { 
                        if(is_admin()) :
                            $server	=	NBR_ROOT_DIR;
                            $server	=	explode("/",$server);
                            array_pop($server);
                            //print_r($server);
                            
                            $server	=	implode("/", $server);
                                        
                            if ($handle = opendir(NBR_ROOT_DIR)) {
                                
                                /* This is the correct way to loop over the directory. */
                                while (false !== ($entry = readdir($handle))) {
                                        if(is_dir(NBR_ROOT_DIR . "/$entry") && $entry !== '.'  && $entry !== '..') $select_root[$entry]	=	$entry;
                                    }
                            
                                    closedir($handle);
                                }
                             ?>
                <div style="display: inline-block; width: 100%;">
                    <form action="" method="post" enctype="application/x-www-form-urlencoded">
                        <input type="hidden" name="action" value="run" />
                        <p>Your software will install into the following folder:</p>
                        <select name="install_location" style="height: 30px; font-size: 16px;">
                            <option value="<?php echo NBR_ROOT_DIR; ?>">Root Folder (perimissions: <?php echo substr(sprintf('%o', fileperms(NBR_ROOT_DIR)), -4); ?>)</option>
                            <?php foreach($select_root as $keys => $values) {
										$dir	=	NBR_ROOT_DIR . "/$keys";
										if(is_dir($dir)) : ?>
                            <option value="<?php echo $dir; ?>"><?php echo "/$keys"; ?> (perimissions: <?php echo substr(sprintf('%o', fileperms($dir)), -4); ?>)</option>
                            <?php 		endif;
									} ?>
                        </select>
                        
                        <p style="margin-top: 15px;">If reinstalling, check to confirm overwrite. <input type="checkbox" name="overwrite" /></p>
                        <div class="base_button"><input disabled="disabled" type="submit" name="submit" value="Install" style="margin-top: 10px;" /></div>
                    </form>
                </div><?php 
                
                        endif;
                    } ?>
        	</div>
    	</div>
	</div><?php
						endif;
						}
				}
		}