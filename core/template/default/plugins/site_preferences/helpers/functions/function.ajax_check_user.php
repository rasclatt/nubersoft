<?php

	function ajax_check_user()
		{
			ob_start();
			autoload_function('nquery,printpre');
			$nubquery	=	nquery();
			
			if(isset($_POST['email']))
				$check['email']		=	$nubquery	->select("ID")
													->from("users")
													->where(array("email"=>$_POST['email']))
													->fetch();
			
			if(isset($_POST['username']))
				$check['username']	=	$nubquery	->select("ID")
													->from("users")
													->where(array("username"=>$_POST['username']))
													->fetch();
			
			$counter[]	=	(isset($check['email']) && $check['email'] == 0)? 0:1;
			$counter[]	=	(isset($check['username']) && $check['username'] == 0)? 0:1;
			// Words to filter
			$filter[]	=	'admin';
			$filter[]	=	'webmaster';
			$filter[]	=	'fuck';
			$filter[]	=	'nuber';
			$filter[]	=	'test';
			$filter[]	=	'administrator';
			$filter[]	=	'nubersoft';
			
			$validUser	=	true;
			// Match filtered
			if(isset($_POST['username']) && !empty($_POST['username'])) {
					$username	=	trim($_POST['username']);
					$filter		=	implode("|",$filter);
					$validUser	=	(preg_match("/$filter/i",$username))? false:true;
				}
			// If filters don't match
			$valid		=	(array_sum($counter) == 0);
?>
<script>
	<?php  if(!$valid || !$validUser) { ?>
	$('#sign_up_button').prop('disabled',true);
	$("#use-error-block").css({"display":"inline-block"});
	<?php } else { ?>
	$('#sign_up_button').prop('disabled',false);
	$("#use-error-block").hide();
	<?php } ?>
</script>
<?php		$data	=	ob_get_contents();
			ob_end_clean();
			
			return $data;
		}