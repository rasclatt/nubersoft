<?php
	class TimeStamp
		{
			protected	$db;

			public		$row_count;
			protected	$_time;
			protected	$users;
			protected	$nubsql;
			
			private		$run;
			
			public	function __construct($_time = 80)
				{
					register_use(__METHOD__);
					
					AutoloadFunction('is_admin,nQuery');
					// Interval
					$this->_time	=	$_time;
					// Save and open database
					$this->Initialize();
				}
			
			public	function Initialize()
				{
					register_use(__METHOD__);
					
					if(isset($_SESSION['username'])) {
							// Record into db
							$this->Record($_SESSION['username']);
							
							// Check db, print users
							$this->users	=	$this->fetch($_SESSION['username']);
						}
				}
			
			public	function AdminTools()
				{
					register_use(__METHOD__);
					
					if(is_admin())  { ?>

			<div style="padding: 30px; background-color: #EBEBEB; max-width: 1100px; margin: 0 auto; margin-bottom: 30px;">
				<table>
					<tr>
						<td colspan="2">
							<h2>Logged in Users</h2>
						</td>
					</tr>
					<tr>
						<td class="ticker-bigs"><?php echo str_pad($this->row_count,2,0,STR_PAD_LEFT); ?></td>
						<td class="ticker-bigs"><?php echo $this->_time; ?></td>
					</tr>
					<tr>
						<td style="text-align: center;">LOGGED IN</td>
						<td style="text-align: center;">TIME SPAN</td>
					</tr>
				</table>
					<style>
					.ticker-bigs	{ background-color: #888; color: #FFF; text-shadow: 1px 1px 4px #000; font-size: 30px; font-family: Arial, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: bold; text-align: center; padding: 20px; }
					#timestamp-table td { vertical-align: top; padding: 10px; border-bottom: 3px solid #666; border-top: 1px solid #FFF; background: linear-gradient(#FFF,#777); }
					#timestamp-table td:first-child { border-bottom: 3px solid #000; background-color: #222; background: linear-gradient(#999,#111);  color: #FFF; text-shadow: 1px 1px 3px #000; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
					#timestamp-table td:last-child { border-top-right-radius: 4px; border-bottom-right-radius: 4px; }
					#timestamp-table .time-headers,
					#timestamp-table p { margin: 0; padding: 0; line-height: normal; font-family: Arial, "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 12px; }
					#timestamp-table .time-headers { font-family: Arial, "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 14px; font-weight: bold; }
					</style>
					<table cellpadding="0" cellspacing="0" border="0" id="timestamp-table">
					<?php
						if(is_array($this->users)) {
								foreach($this->users as $user) { ?>
								<tr>
									<td>
										<p class="time-headers"><?php echo $user['username']; ?></p>
										
									</td>
									<td style="text-shadow: 1px 1px 2px #FFF;">
										<p>Last Logged In: <?php echo date("M d, g:i A",strtotime($user['timestamp'])); ?>
										<?php if(is_numeric($user['username'])) { ?>
											 <br /><?php echo (isset($user['email']))? $user['email']:"Not Set Yet"; ?></p>
											<?php	} ?>
									</td>
									<td>
										<?php echo (isset($user['ip_address']))? $user['ip_address']:"N/A"; ?>
									</td>
									<td>
										<div style="height: 15px; width: 15px; border: 1px solid #CCC; box-shadow: 1px 1px 3px #000; background-color: green;"></div>
									</td>
								</tr>
								<?php 		}
									} ?>
					</table>
			</div><?php		}	
				}
			
			// This should grab all users connected
			public	function Fetch()
				{
					if(empty($this->run))
						return false;
					
					$interval = $this->_time;
					
					// This is just checking a time range and collecting names
					// You may want to make a new function that will then take the return list and query your user info table to get the user info
					$now	=	date("Y-m-d H:i:s",strtotime("now"));
					$query	=	nQuery();
							
					if(!$query)
						return false;
					
					$users	=	$query	->select()
										->from("members_connected")
										->addCustom("where timestamp > DATE_SUB('$now', INTERVAL $interval MINUTE)")
										->orderBy(array("timestamp"=>"DESC"))
										->fetch();
					
					// This should get the count
					$this->row_count	=	(isset($users))? count($users):0;
					
					// Return if users are available
					return (isset($users))? $users:0;
				}
			
			public	function Record($_user)
				{
					AutoloadFunction("FetchUniqueId,filter_action_words,combine_arrays");
					
					if(isset(NubeData::$settings->timestamp_logged))
						return NubeData::$settings->timestamp_logged;
					
					//$connected	=	$this->db->prepare("INSERT INTO members_connected (`nom`, `timestamp`) VALUES (:nom,NOW()) ON DUPLICATE KEY UPDATE timestamp = NOW()");
					$payload["timestamp"]	=	date("Y-m-d H:i:s",strtotime("now"));
					$payload["username"]	=	$_user;
					$payload["unique_id"]	=	FetchUniqueId();
					$payload["ip_address"]	=	$_SERVER['REMOTE_ADDR'];
					try {
							// Filter out all resvered keys from post array
							$filter_post	=	array_diff_key($payload,filter_action_words('key'));
							// Filter out all resvered keys from file array
							$payload		=	array_diff_key($payload,filter_action_words('key'));
							// Combine all arrays and filter out empty
							$final			=	combine_arrays($payload,$filter_post,true);
							$query			=	nQuery();
							
							if(!$query)
								return false;
								
							$connected		=	$query	->insert("members_connected")
														->setColumns(array_keys($final))
														->setValues(array($final))
														->addCustom("ON DUPLICATE KEY UPDATE timestamp = '{$payload['timestamp']}'")
														->write();
							
							nApp::saveSetting('timestamp_logged',true);
						}
					catch (Exception $e)
						{
							AutoloadFunction('create_default_timestamp');
							create_default_timestamp(array("create"=>true));
							nApp::saveSetting('timestamp_logged',true);
						}
				}
		}