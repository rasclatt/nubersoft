<?php
$prefs	=	$this->getDataNode();
$err	=	$this->toArray(self::call()->getIncidental('login'));
$nForm	=	$this->getHelper('nForm');
?>
<div class="login_container nFont">
	<div class="login_bkg">
		<div id="login">
			<form method="post" action="#" enctype="application/x-www-form-urlencoded" id="loginForm">
			<div style="text-align: left; display: inline-block;">
				<div class="login_fields">
					<?php echo $nForm->text(array('name'=>'username','other'=>array('required','autocomplete="off"'),'label'=>'Username')); ?>
				</div>
				<div class="login_fields">
					<?php echo $nForm->password(array('name'=>'password','type'=>'password','other'=>array('required','autocomplete="off"'),'label'=>'Password','id'=>'password')); ?>
				</div>
			</div>
				<?php
				echo strip_tags($nForm->fullHide(array('name'=>'action','value'=>'login','id'=>'action')),'<input>').strip_tags($nForm->fullHide(array('name'=>'token[login]','value'=>self::call('nToken')->getSetToken('login'))),'<input>');
				?>
				<div class="nbr_contain">
						<div class="nbr_button"><input disabled="disabled" type="submit" name="login" value="Login" id="loginsubmit" class="disabled-submit" /></div>
				</div>
			</form>
				<?php
				if($this->siteValid())
					$turn_on_signup	=	(isset($prefs->preferences->site->content->sign_up->toggle) && $prefs->preferences->site->content->sign_up->toggle == 'on')? true:false;
				
				$turn_on_signup	=	(!isset($turn_on_signup	))? false:$turn_on_signup;
				if($turn_on_signup) {
?>
				<p class="after_text nFont">Not a member? <span class="after_link nButton" data-acton="#sign_up" data-hide="#login">Sign up now!</span></p>
<?php			}
?>
			</div>
<?php						if($turn_on_signup)
					require_once(__DIR__.DS.'site.signup.php');
				
				if(!empty($error['general']))
					echo $error['general'];

				if(self::$redirect !== false && self::$redirect !== 0) {
?>
			<div class="nbr_contain">
				<p class="smaller_print nFont">Log in above or go to <a href="<?php echo self::$redirect['link'];?>"><?php echo self::$redirect['name'];?>.</a></p>
			</div>
<?php	}
		if(isset($err[0])) { ?>
<div style="display: inline-block; margin: 0; padding:0; position: relative; max-width: 400px;">
	<div class="nbr_error_msg">
		<?php echo wordwrap(ucfirst($err[0]['error']),40,'<br />'); ?>
	</div>
</div>
<?php
				}
?>		</div>
	</div>