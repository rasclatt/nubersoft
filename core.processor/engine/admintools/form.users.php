<?php
	if(!function_exists("AutoloadFunction"))
		return;
	
	$nProcToken	=	nApp::nToken()->setMultiToken('nProcessor','formusers');
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
						<?php Input(false,'ID',false,'fullhide',$dropdowns); ?>
						<?php Input(false,'unique_id',false,'fullhide',$dropdowns); ?>
						<table style="display: inline-block;">
							<tr>
								<td class="padding-bottom-15">
									<label for="username">Username</label>
									<?php Input(false,'username',false,'text',$dropdowns); ?>
								</td>
								<td class="padding-bottom-15">
									<label for="password">Password</label>
									<?php Input(false,'password',false,'password',$dropdowns); ?>
								</td>
							</tr>
							<tr>
								<td class="padding-bottom-15">
									<label for="firstname">First Name</label>
									<div class="login_fields"><?php Input(false,'first_name',false,'text',$dropdowns); ?></div>
								</td>
								<td class="padding-bottom-15">
									<label for="lastname">Last Name</label>
									<div class="login_fields"><?php Input(false,'last_name',false,'text',$dropdowns); ?></div>
								</td>
							</tr>
							<tr>
								<td class="padding-bottom-15">
									<label for="email">E-mail</label>
									<div class="login_fields"><?php Input(false,'email',false,'text',$dropdowns); ?></div>
								</td>
								<td class="padding-bottom-15">
									<label for="page_live">Active</label>
									<div class="login_fields"><?php Input(false,'page_live',false,'select',$dropdowns); ?></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">Usergroup<div class="login_fields"><?php Input(false,'usergroup',false,'select',$dropdowns); ?></div></td>
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
							ID: <?php Input($row,'ID',false,'hidden',$dropdowns); ?>
						</label>
						<?php Input($row,'unique_id',false,'fullhide',$dropdowns); ?>
						<input type="hidden" name="thumbnail" value="1" />
						<table style="display: inline-block;">
							<tr>
								<td class="nbr_user_plugin">Username</td><td><div class="login_fields"><?php Input($row,'username',false,'text',$dropdowns); ?></div></td>
								<td rowspan="7" style="padding: 20px;vertical-align: top;">
									<div style="border: 1px solid #666; height: 100px; width: 100px; background-color: #888;<?php if(!empty($row['file_name'])) { ?>background-image: url(<?php echo '/client_assets/thumbs/users/'.$row['file_name']; ?>); background-repeat: no-repeat; background-size: cover;<?php } ?>">
									</div>
									<?php Input($row,'file',false,'file',$dropdowns,$nuber); ?>
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
								<td class="nbr_user_plugin">Password</td><td><div class="login_fields"><?php Input($row,'password',false,'password',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">First Name</td><td><div class="login_fields"><?php Input($row,'first_name',false,'text',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Last Name</td><td><div class="login_fields"><?php Input($row,'last_name',false,'text',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">E-mail</td><td><div class="login_fields"><?php Input($row,'email',false,'text',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Usergroup</td><td><div class="login_fields"><?php Input($row,'usergroup',false,'select',$dropdowns); ?></div></td>
							</tr>
							<tr>
								<td class="nbr_user_plugin">Page</td><td><div class="login_fields"><?php Input($row,'page_live',false,'select',$dropdowns); ?></div></td>
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
	