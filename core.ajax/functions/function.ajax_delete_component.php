<?php
function ajax_delete_component()
	{
		ob_start();
		$query	=	nQuery();
		$query	->delete()
				->from("components")
				->where(array("ID"=>$_REQUEST['id']))
				->write();
		
		$entry	=	$query	->select("ID")
							->from("components")
							->where(array("ID"=>$_REQUEST['id']))
							->fetch();
	
		$msg	=	($entry == 0)? "Deleted":"Error!";	
?>	<h3><?php echo $msg; ?></h3>
<?php	$data	=	ob_get_contents();
		ob_end_clean();
		
		return $data;
	}