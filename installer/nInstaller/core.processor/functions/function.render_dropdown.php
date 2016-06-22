<?php
	function render_dropdown($column = 'page_live')
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery');
			$nubquery	=	nQuery();
			$values		=	$nubquery	->select(array("menuName","menuVal"))
										->from("dropdown_menus")
										->where(array("assoc_column"=>$column))
										->orderBy(array("page_order"=>"ASC"))
										->Record(__FILE__)
										->fetch();
			ob_start();
			if($values != 0) {
					foreach($values as $options) { ?>
				<option value="<?php echo $options['menuVal']; ?>"><?php echo $options['menuName']; ?></option>
					<?php }	
				}
			$data	=	ob_get_contents();
			ob_end_clean();
			return $data;
		}
?>