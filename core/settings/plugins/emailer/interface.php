<?php
$Emailer	=	$this->getPlugin('\Emailer\Component');
$emails		=	$Emailer->getAllEmailReceipts(1,100);
$ids		=	(is_array($emails))? array_map(function($v){ return $v['ID']; },$emails) : array();
$emails		=	$Emailer->fetchColumns($emails,array('to','email_from','ip','timestamp','header','subject','raw_message'));
$trashed	=	$this->getSession('email_receipt',true);
?>
<style>
.email_details td,
.email_message td {
	text-align: left;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 16px;
	padding: 5px;
}
.email_message td {
	border-bottom: 3px solid #CCC;
}
tr.email_headers th {
	background-color: #333;
	color: #fff;
	text-shadow: 1px 1px 3px #000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 20px;
	padding: 8px;
}
.allblocks h1,
.allblocks h2,
.allblocks h3 {
	font-family: Arial, Helvetica, sans-serif;
	color: red;
}
.email_details a:link,
.email_details a:visited,
.email_details a:hover {
	cursor: pointer;
	color: inherit;
	font-size: inherit;
	cursor: pointer;
}
.email_details h3,
.email_details p {
	color: #666;
	font-size: 14px;
	line-height: 20px;
	margin: 0;
}
.email_details h3 {
	font-size: 16px;
	line-height: 20px;
}
.email_message {
	padding: 15px;
	background-color: #DFDBD3;
	vertical-align: top;
	min-width: 500px;
	max-width: 800px;
	max-height: 300px;
	overflow: auto;
}
tr.email_details td:first-child,
tr.email_details td:last-child {
	background-color: #EBEBEB;
}
tr.email_details td {
	vertical-align: top;
	border-bottom: 1px dashed #666;
	padding: 10px;
}
tr.email_details td:last-child {
	vertical-align: middle;
}
tr.email_details:hover td {
	background-color: #CCC;
}
#help {
	height: 30px;
	padding: 10px 20px;
}
.highlight_help {
	background-color: #F60;
	color: #FFF;
	text-shadow: 1px 1px 3px #000;
	font-family: Arial, Helvetica, sans-serif;
}
</style>
<div style="background: linear-gradient(#000,#333); display: inline-block; width: 100%; padding-bottom: 30px;">
<div class="allblocks">
	<form action="" method="post" />
		<table cellpadding="0" cellspacing="0" border="0" style=" width: 100%;">
			<tr>
				<td colspan="3" id="help"<?php if(!empty($trashed)) echo ' class="highlight_help temp_fade"' ?>>
					<?php if(!empty($trashed)) echo 'Trash has been emptied.' ?>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h2 style="display: inline-block;">Email Receipts</h2>
				<?php $count = $this->nQuery()->query("select COUNT(*) as count from components where category_id = 'email_receipt' and `page_live` = 'off'")->getResults(true);
				
				if($count['count'] > 0) {
				 ?>
				<a style="display: inline-block; font-family: Arial, Helvetica, sans-serif; margin-left: 20px; font-size: 14px; color: #FFF;" href="<?php echo $this->siteUrl('/contact-manager/?action=nbr_email_receipts_delete') ?>">Empty Trash (<?php echo $count['count'] ?>)</a>
				<?php } ?>
				</td>
				<td style="color: #FFF; font-family: Arial, Helvetica, sans-serif;">Delete all <input type="checkbox" name="delete_all_email" /></td>
			</tr>
			<tr class="email_headers">
				<th>Details</th>
				<th>Message</th>
				<th>Trash</th>
			</tr>
		<?php
		foreach($emails as $key => $email) {
			$flag	=	(strpos($email['raw_message'],'<') !== false);
		?>
			<tr class="email_details">
				<td>
					<h3><?php echo $email['subject'] ?></h3>
					<p><a href="mailto:<?php echo $email['email_from'] ?>"><?php echo $email['email_from'] ?></a><br /><?php echo $email['ip'] ?></p>
					<p><?php echo date('D, F j, Y (g:ia)',strtotime($email['timestamp'])) ?></p>
				</td>
				<td class="email_message">
					<?php echo strip_tags($email['raw_message']) ?>
				</td>
				<td style="text-align: center;">
					<input type="checkbox" name="delete_email[]" value="<?php echo $ids[$key] ?>" <?php if($flag) echo 'checked' ?>/>
					<?php if($flag) echo '<p style="border-radius: 4px; color: #FFF; font-size: 12px; background-color: red; padding: 3px; " class="junkmail">Junk Mail</p>' ?>
				</td>
			</tr>
		<?php
		}
		if(empty($emails)) {
		?>
			<tr class="email_details">
				<td colspan="3">No messages.</td>
			</tr>
		<?php } ?>
		</table>
		<input type="hidden" name="action" value="nbr_email_remove_by_ip" />
		<div class="nbr_button small" style="margin-top: 30px;">
			<input type="submit" value="Move to Trash" />
		</div>
		<a class="nbr_button small" style="font-family: Arial, Helvetica, sans-serif;margin-top: 30px;" href="<?php echo $this->siteUrl('/contact-manager/') ?>">Reload</a>
	</form>
</div>
</div>
<script>
$(document).ready(function() {
	$('input[name=delete_all_email]').click(function() {
		var	allChecked	=	false;
		var isChecked	=	$(this).is(":checked");
		if(isChecked)
			allChecked	=	true;
			
		$('input[name=delete_email\\[\\]]').prop("checked",allChecked);
	});
	
	$('.temp_fade').delay(2500).fadeOut('fast',function() {
		$(this).html('').removeClass('highlight_help');	
	});
	
	$('.junkmail').hover(
		function() {
			$('#help').addClass('highlight_help').html('Contains characters that indicate user was attempting to hack the website.');
		},
		function() {
			$('#help').html('').removeClass('highlight_help');
	});
});
</script>