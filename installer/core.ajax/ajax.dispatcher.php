<?php
include(__DIR__."/../config.php");

if(!is_admin())
	return;

AutoloadFunction("check_empty");

if(check_empty($_POST,'action','clear_form')) {
	$delEngine	=	new recursiveDelete();
	$delEngine->delete(NBR_ROOT_DIR.'/setup/');
	if(isset($_SESSION['install']))
		unset($_SESSION['install']);
	if(isset($_SESSION['install_key']))
		unset($_SESSION['install_key']);
?>		<h2>CLEARED FORM!</h2>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="1" value="START AGAIN" /></div>
			</li>
		</ul>
<?php
}
elseif(check_empty($_POST,'action','get_step')) {
	$step	=	$_POST['app']['step'];
	
	if(is_file($inc = NBR_ROOT_DIR."/steps/go{$step}.php"))
		include($inc);
	else {
?>		<p>INVALID COMMAND</p>
		<ul class="installer">
			<li>
				<div class="nbr_button"><input type="submit" name="next" data-nextstep="<?php $sub = ($step-1); echo ($sub <= 1)? 1 : $sub; ?>" value="BACK" /></div>
			</li>
		</ul>
<?php
	}
}