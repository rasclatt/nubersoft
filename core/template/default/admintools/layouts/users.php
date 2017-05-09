<?php
use Nubersoft\NubeData as NubeData;

	if(!function_exists("autoload_function"))
		return;
	
	$nProcToken	=	self::call('nToken')->setMultiToken('nProcessor','formusers');
?>	<div class="fullscreen">
		<div class="misc-window" style="padding-bottom: 0; background-color: #CCC; border-radius: 0; border: 5px solid #FFF;">			
				
			<div class="misc-window-cont nbr_general_form">
				<div style="float: right; display: inline-block;">
					<div class="close-button" onClick="ShowHide('.fullscreen','','fade')">x</div>
				</div>
				<div style="padding: 20px; text-align: left;" class="nbr_user_plugin">
				<h2>Add User</h2>
					<form enctype="multipart/form-data" method="post" action="">
						<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
						<input type="hidden" name="requestTable" value="users" />
						<?php Input(array('name'=>'ID','type'=>'fullhide')); ?>
						<?php Input(array('name'=>'unique_id','type'=>'fullhide')); ?>
						<table style="display: inline-block;">
							<tr>
								<td class="padding-bottom-15">
									<label for="username">Username</label>
									<?php Input(array('name'=>'username','placeholder'=>'Username')); ?>
								</td>
								<td class="padding-bottom-15">
									<label for="password">Password</label>
									<?php Input(array('name'=>'password','type'=>'password','placeholder'=>'Password')); ?>
								</td>
							</tr>
							<tr>
								<td class="padding-bottom-15">
									<label for="firstname">First Name</label>
									<div class="login_fields"><?php Input(array('name'=>'first_name')); ?></div>
								</td>
								<td class="padding-bottom-15">
									<label for="lastname">Last Name</label>
									<div class="login_fields"><?php Input(array('name'=>'last_name')); ?></div>
								</td>
							</tr>
							<tr>
								<td class="padding-bottom-15">
									<label for="email">E-mail</label>
									<div class="login_fields"><?php Input(array('name'=>'email')); ?></div>
								</td>
								<td class="padding-bottom-15">
									<label for="page_live">Active</label>
									<div class="login_fields"><?php Input(array('name'=>'page_live','type'=>'select','options'=>$dropdowns)); ?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">Usergroup<div class="login_fields"><?php Input(array('name'=>'usergroup','type'=>'select','options'=>$dropdowns)); ?></div></td>
							</tr>
							<tr>
								<td style="vertical-align: bottom; text-align: center;" colspan="2">
									<div style="float: none; display: inline-block; margin: 0px auto;">
										<div class="nbr_button"><input disabled="disabled" type="submit" name="add" value="ADD USER" /></div>
									</div>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
	<h2><div class="close-button" onClick="ShowHide('.fullscreen','','fade')">+ADD</div>Viewing: Users</h2>
	<table class="data_tables" cellpadding="0" cellspacing="0" border="0">
		<tr class="header_row"><!--
			<td>ID</td>
			<td>UNIQUE ID</td>-->
			<td>USERNAME</td>
			<td>FIRST NAME</td>
			<td>LAST NAME</td>
			<td>EMAIL ADDRESS</td>
			<td>ACTIVE</td>
		</tr>
<?php

	if($values == 0) {
		echo '
		<tr>
			<td colspan="5" style="padding: 30px; background-color: red; color: #FFF;text-shadow: 1px 1px 3px #000;">
				<h1>You have to add yourself as an admin user!</h1>
				<p>If you log out, you will not be able to log back in again.</p>
				<p>Click the <code>+ ADD</code> to add a user.</p>
			</td>
		</tr>';
	}
	else {
		foreach($values as $row) {
?>		<tr class="data_row" onClick="PowerButton('u_<?php echo $row['ID']; ?>','default','.hidethis')"><!--
			<td><?php echo $row['ID']; ?></td>
			<td><?php echo $row['unique_id']; ?></td>-->
			<td><?php echo $row['username']; ?></td>
			<td><?php echo $row['first_name']; ?></td>
			<td><?php echo $row['last_name']; ?></td>
			<td><?php echo $row['email']; ?></td>
			<td><?php echo $row['page_live']; ?></td>
		</tr>
		<tr>
			<td colspan="6" style="vertical-align: bottom; text-align: left;">
				<div id="u_<?php echo $row['ID']; ?>_panel" class="hidethis inset-box nbr_general_form nbr_user_plugin" style="padding: 20px; width: auto;">
					<h3 style="background-color: #666; color: #FFF; display: block; width: auto; padding: 15px; border-radius: 4px; margin: 0 0 20px 0;">User Profile: <?php echo (!empty($row['email']))? $row['email']:$row['username']; ?></h3>
					<form enctype="multipart/form-data" method="post">
						<input type="hidden" name="requestTable" value="users" />
						<input type="hidden" name="token[nProcessor]" value="<?php echo $nProcToken; ?>" />
						<label>
							ID: <?php Input(array('value'=>$row,'name'=>'ID','type'=>'hidden')); ?>
						</label>
						<?php Input(array('value'=>$row,'name'=>'unique_id','type'=>'fullhide')); ?>
						<input type="hidden" name="thumbnail" value="1" />
						<table style="display: inline-block;">
							<tr>
								<td class="nbr_user_plugin">Username</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'username','placeholder'=>'username')); ?>
									</div>
								</td>
								<td rowspan="7" style="padding: 20px;vertical-align: top;">
									<div style="border: 1px solid #666; height: 100px; width: 100px; background-color: #888;<?php if(!empty($row['file_name'])) { ?>background-image: url(<?php echo '/client/thumbs/users/'.$row['file_name']; ?>); background-repeat: no-repeat; background-size: cover;<?php } ?>">
									</div>
									<?php Input(array('value'=>$row,'name'=>'file','type'=>'file')); ?>
									<div style="float: left; display: inline-block; background-color: #888; border-radius: 6px;">
									<table cellpadding="0" cellspacing="0" border="0" style="margin: 10px;">
										<tr>
											<td>
												<div class="nbr_button" style=" float: right;">
													<input disabled="disabled" type="submit" name="update" value="SAVE" />
												</div>
											</td>
										</tr>
										<tr>
											<td>
										<label style="display: inline-block; width: auto; float: right;">Delete User?
											<input type="checkbox" name="delete" />
										</label>
											</td>
										</tr>
									</table>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Password</td>
								<td>
									<div class="login_fields">
										<?php Input(array('name'=>'password','type'=>'password','placeholder'=>'password')); ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">First Name</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'first_name','placeholder'=>'First Name')); ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Last Name</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'last_name','placeholder'=>'Last Name')); ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">E-mail</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'email','placeholder'=>'E-mail')); ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Usergroup</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'usergroup','type'=>'select','options'=>$dropdowns)); ?>
									</div>
								</td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Active</td>
								<td>
									<div class="login_fields">
										<?php Input(array('value'=>$row,'name'=>'page_live','type'=>'select','options'=>$dropdowns)); ?>
									</div>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</td>
		</tr>
			<?php }
		} ?>
	</table>
<style>
td.nbr_user_plugin,
.nbr_user_plugin label	{
	font-size: 12px !important;
	color: #333 !important;
	text-shadow: none;
}
</style>
	