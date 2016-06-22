<?php
	
	include_once(__DIR__.'/../config.php');
	
	if(!is_admin(1))
		return;
	
	class	RolesEngine
		{
			protected	$nuber;
			
			public	function __construct()
				{
					AutoloadFunction("nQuery");
					$this->nuber	=	nQuery();
					
					// Load all appropriate functions
					AutoloadFunction('process_requests,default_jQuery,check_empty');
					// Add role to database if update or add
					if(!empty($_POST['update']) || !empty($_POST['add']))
						$this->AddRole()->execute();
				}
			
			public	function AddRole()
				{
					if(check_empty($_POST,'save_new','on')) {
							unset($_POST['ID'],$_POST['unique_id']);
							$check	=	true;
						}
					elseif(!empty($_POST['add']))
						$check	=	true;
					else
						$check	=	false;
						
					if($check == true) {
							$role_set	=	$this->CheckUsergroup($_POST['role']);
							
							if($role_set) {
									global $_error;
									$_error['role']	=	'Usergroup already set.';
									
									if(isset($_POST['add']))
										unset($_POST['add']);
									
									return $this;
								}
						}
						
					$content['read']		=	(!empty($_POST['read']))? $_POST['read']:"";
					$content['write']		=	(!empty($_POST['write']))? $_POST['write']:"";
					$content['description']	=	(!empty($_POST['description']))? $_POST['description']:"Untitled";
					$_POST['usergroup']		=	(int) $_POST['role'];
					unset($_POST['read'],$_POST['write'],$_POST['role'],$_POST['description']);

					// Serialize data
					$_POST['content']		=	serialize($content);
						
					return $this;
				}
			
			public	function CheckUsergroup($role = 0)
				{
					$use_set	=	nQuery()->select(array("content","ID"))
											->from("system_settings")
											->where(array("usergroup"=>$role,"name"=>"permissions"))
											->DecodeColumns(array("content"))
											->fetch();
					if($use_set != 0) {
						$uSetCnt	=	count($use_set);
						for($i=0;$i < $uSetCnt; $i++) {
							if($_POST['description'] == $use_set[$i]['content']['description'])
								return true;
						}
					}
					
					return false;
				}
			
			public	function execute()
				{
					process_requests();
				}
			
			public	function UserForm($users = false)
				{
					$query	=	nQuery();
					 ?>
		ADD
		<div class="login_fields">
		<form id="add_role" method="post" class="submit-form">
			<input type="hidden" name="requestTable" value="system_settings" />
			<input type="hidden" name="name" value="permissions" />
			<input type="text" name="description" placeholder="Role Name (like &quot;Administrator&quot;)" />
			<label>New Role (lower has more access)</label>
			<select name="role">
				<?php for($a = 1; $a < 30; $a++) { ?>
				<option value="<?php echo $a; ?>"><?php echo $a; ?></option>
				<?php } ?>
			</select>
			</label>
			<input type="hidden" name="add" value="silent_add" />
			<input disabled="disabled" type="submit" name="add" value="ADD NEW" />
		</form>
		UPDATE
		<form id="permissions" method="post" class="submit-form">
			<input type="hidden" name="requestTable" value="system_settings" />
			<input type="hidden" name="name" value="permissions" />
			<input type="hidden" name="ID" value="<?php echo (!empty($users['ID']))? $users['ID']:""; ?>" />
			<input type="hidden" name="unique_id" value="<?php echo (!empty($users['unique_id']))? $users['unique_id']:""; ?>" />
			
			<h2>User Role</h2>
			<?php if(isset($users['usergroup'])) { ?>
			<input type="hidden" name="role" value="<?php echo $users['usergroup']; ?>" />
			<input type="text" name="description" value="<?php echo (isset($users['content']['description']) && !empty($users['content']['description']))? $users['content']['description']:"Untitled Usergroup"; ?>" /> is role <?php echo $users['usergroup']; ?>.
			<?php }
			
			AutoloadFunction('get_tables_in_db');
			$tables	=	get_tables_in_db(); ?>
			<table cellpadding="0" cellspacing="0" border="0">
			<?php
			$tCount	=	count($tables);
			for($i=0; $i < $tCount; $i++) {
						$table 		=	$tables[$i];
						$tble_check	=	(isset($users['content']['read'][$table]) && !empty($users['content']['read'][$table]))? true:false;
						 ?>
				<tr>
					<td class="table-names"<?php if(($i%2) == 0) echo ' style="background-color: #EBEBEB;"'; ?>>
						<div>
						<label>
							<input class="checkItem checkRead" type="checkbox" name="read[<?php echo $table = $tables[$i]; ?>]"<?php if($tble_check) { echo ' checked="checked"'; } ?> />
							<?php echo ucwords(str_replace("_"," ",strtolower($table))); ?></label>
						</div>
					</td>
					<td class="table-names"<?php if(($i%2) == 0) echo ' style="background-color: #EBEBEB;"'; ?>>READ</td>
					<td rowspan="2">
						<div onClick="PowerButton('<?php echo $table; ?>','slide','.hideprefs')" class="cursorPointer" style="padding: 10px;">EDIT COLUMNS</div>
						<div id="<?php echo $table; ?>_panel" class="hideprefs" style="display: <?php echo ($tble_check && $users['content']['read'][$table] != 'on')? "block;":"none;"; ?> background-color: #655C55; color: #FFF; text-shadow: 1px solid #000;">
						<table cellpadding="0" cellspacing="0" border="0">
						<?php $cols	=	$query->describe($table)->fetch();
							$z = 1;
							$cCount	=	count($cols);
							for($c = 0; $c < $cCount; $c++) {
								if($z == 1)
									echo "<tr>"; ?>
							<td>
								<table cellpadding="0" cellspacing="0" border="0" style="border-bottom: 1px solid #CCC; border-right: 1px solid #CCC; min-width: 150px;">
									<tr>
										<td style="padding: 5px; font-size: 12px; text-shadow: 1px 1px 2px #000;">
											<?php echo ucwords(str_replace("_"," ",$cols[$c]['Field'])); ?>
										</td>
									</tr>
									<tr>
										<td>
											<label style="display: inline-block; padding: 5px; float: left; display: block; width: auto;">
												read <input type="checkbox" name="read[<?php echo $table; ?>][<?php echo $cols[$c]['Field']; ?>]"<?php if(isset($users['content']['read'][$table][$cols[$c]['Field']]) && !empty($users['content']['read'][$table][$cols[$c]['Field']])) { echo ' checked="checked"'; } ?> style=" display: block; width: auto;" />
											</label>
											<label style="display: inline-block; padding: 5px; float: left;">
												write <input type="checkbox" name="write[<?php echo $table; ?>][<?php echo $cols[$c]['Field']; ?>]"<?php if(isset($users['content']['write'][$table][$cols[$c]['Field']]) && !empty($users['content']['write'][$table][$cols[$c]['Field']])) { echo ' checked="checked"'; } ?> style=" display: block; width: auto;" />
											</label>
										</td>
									</tr>
								</table>
								
							</td>
							<?php	
									if($z == 3) {
											echo "</tr>";
											$z	=	0;
										}
									$z++; 
								}
						 ?>
						 </table>
						</div>
					</td>
				</tr>
				<tr>
					<td class="table-names"<?php if(($i%2) == 0) echo ' style="background-color: #EBEBEB;"'; ?>>
						<div>
						<label>
							<input class="checkItem checkWrite" type="checkbox" name="write[<?php echo $tables[$i]; ?>]"<?php if(isset($users['content']['write'][$table]) && !empty($users['content']['write'][$table])) { echo ' checked="checked"'; } ?> />
							<?php echo ucwords(str_replace("_"," ",strtolower($table))); ?>
						</label>
						</div></td>
					<td class="table-names"<?php if(($i%2) == 0) echo ' style="background-color: #EBEBEB;"'; ?>>WRITE</td>
				</tr>
			
			<?php }
			 ?>
			</table>
			<?php if((isset($users['ID']) && $users['usergroup'] != 1) || is_admin(1)) { ?>
			<label>DELETE?
			<input type="checkbox" name="delete" />
			</label>
			<?php } ?>
			<input type="hidden" name="<?php echo (!isset($users['ID']))? "add":"update"; ?>" value="silent_save" />
			<div class="login_button">
				<input disabled="disabled" type="submit" name="<?php echo (!isset($users['ID']))? "add":"update"; ?>" id="submit_btn" value="SAVE" />
			</div>
		</form>
		</div>
					<?php
				}
		}

	// Autoload functions
	AutoloadFunction('default_jQuery,check_empty,organize,get_user_roles,nQuery');
	$userDef		=	(isset($_POST['role']))? array("name"=>"permissions","usergroup"=>$_POST['role']):array("name"=>"permissions");
	$RolesProcessor	=	new RolesEngine();
	$query			=	nQuery();
	
	if(!empty($_GET['mode']) && !isset($_POST['role']))
		$userDef	=	array("name"=>"permissions","ID"=>$_GET['id']);
		
	$users		=	$query->select(array("ID","unique_id","name","content","usergroup"))->from("system_settings")->where($userDef)->fetch();  ?>
	
	<div id="form-loader">
	
	<?php
	$all_defs	=	get_user_roles();
	if($all_defs != 0) {
			$aDefsCnt	=	count($all_defs);
			for($a = 0; $a < $aDefsCnt; $a++) {
				 ?>
	<div class="cursorPointer" onClick="AjaxFlex('#form-loader','/core.processor/ajax.engine/set.permissions.php?id=<?php echo $all_defs[$a]['ID']; ?>&mode=focus')"><?php echo "(".$all_defs[$a]['usergroup'].") ".$all_defs[$a]['content']['description']; ?></div>
			<?php
				}
		}
	//echo default_jQuery();
?>
<style>
.table-names	{
	background-color: #CCC;
	border-bottom: 1px solid #888;
	border-top: 1px solid #EBEBEB;
	font-family: Arial, Helvetica, sans-serif;
	padding: 10px 15px;
	font-size: 16px;
	text-shadow: 1px 1px 2px #FFF;
}
</style>
	<label>
	Check
	<select id="checkit" name="checkit">
		<option value="none">None</option> 
		<option value="all">CHECK ALL</option> 
		<option value="read">CHECK READ</option>
		<option value="write">CHECK WRITE</option>
	</select>
	</label>
		<?php if(isset($_POST['requestTable'])) { ?>
		<div id="message-spot">SAVED</div>
		<?php }
		
		// Default
		$user		=	(isset($users[0]))? $users[0]:false;
		
		if($user != false)
			$user['content']	=	json_decode(Safe::decode($user['content']));
			
		$RolesProcessor->UserForm($user); ?>
		
		<?php
		$blogger	=	new PostEngine();
		echo
		$posts		=	$blogger->FetchPostsByParent('20150429001240176555405382350')->prepare()->display;
		?>
	</div>


<script>
$('#checkit').change(function(){  
    var IsChecked = $('#checkit').val();
	
    if (IsChecked == 'all') {
        $('.checkItem').prop('checked',true);
    }
	else if (IsChecked == 'read') {
        $('.checkWrite').prop('checked',false);
        $('.checkRead').prop('checked',true);
		}
	else if (IsChecked == 'write') {
        $('.checkRead').prop('checked',false);
        $('.checkWrite').prop('checked',true);
		}
    else {
        $('.checkItem').prop('checked',false);
    }
});

$("#save_new").on("change",function(){
		var Value	=	$("#save_new").prop("checked");
		
		if(Value == true) {
				$("#submit_btn").val("ADD?");
				$("#submit_btn").attr("name","add");
			}
	});
	
	function SaveForm(ElemId)
		{
			$.ajax({
					url:	'/core.processor/ajax.engine/set.permissions.php',
					data:	ElemId.serialize(),
					type:	'post',
					success: function(data) {
						$("#form-loader").html(data);
					}
				});
				
		}
$(document).ready(function() {
		<?php if($all_defs == 0) { ?>
			$('.checkItem').prop('checked',true);
			SaveForm($("#permissions"));
		<?php } ?>
		
		$(".submit-form").submit(function() {
				
				SaveForm($(this));
				return false;
			});
	});
</script>