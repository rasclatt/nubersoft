<?php
$success	=	$this->getSession('email_contact',true);
$Form		=	$this->getHelper('nForm');

if(!empty($success)) {
	echo $this->safe()->decode($this->getHelper('Emailer')->getReceiptMessage('default'));
	return;
}
?>
<div class="login_container nFont">
	<div class="nbr_login_window" style="text-align: left; max-width: 300px;">
		<?php echo $Form->open(array('action'=>$this->siteUrl('/company/contact/'),'id'=>'email_contact')) ?>
			<?php echo $Form->fullhide(array('name'=>'action','value'=>'nbr_send_contact_email')) ?>
			<?php echo $Form->fullhide(array('name'=>'token','value'=>$this->getHelper('nToken')->create('email_contact'))) ?>
			<?php echo $Form->text(array('name'=>'email','placeholder'=>'Your email address','label'=>'Your email addresss','style'=>'text-align: left; float: left;','value'=>$this->getPost('email'))) ?>
			<div style="margin-top: 10px; text-align: left; display: inline-block; width: 100%;">
				<label>Please leave a question or comment.</label>
				
					<?php echo $Form->textarea(array('name'=>'comment','placeholder'=>'Your comment/question','style'=>'display: block; font-size: 18px; min-height: 100px; max-width: 400px; padding: 5px;','value'=>$this->getPost('comment'))) ?>
			</div>
			<div class="nbr_button">
				<?php echo $Form->submit(array('name'=>'send_email','value'=>'SEND')) ?>
			</div>
		<?php echo $Form->close() ?>
	</div>
	<?php
	$alerts	=	$this->getAlertsByKind('warnings','contact');
	if(!empty($alerts)) {
	?>
	<div style="margin-top: 15px;" class="error_wrapper">
		<div class="nbr_warning" style="background-color: red;"><?php echo implode('</div>'.PHP_EOL.'<div class="nbr_warning is_error" style="background-color: red;">',$alerts) ?></div>
	</div>
	
	<?php
	}
	?>
</div>
<script>
$(document).ready(function() {
	$('.error_wrapper').delay(3500).fadeOut('fast');
	
	$('#email_contact').validate({
		rules:{
			email: {
				email: true,
				required: true
			},
			comment: "required"
		}
	});
});
</script>