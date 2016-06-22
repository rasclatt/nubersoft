<?php
/*Title: app_emailer.php*/
/*Description: This function creates an instance of an email form and processor.
`~app::app_emailer[setting1="value" setting2="value2"]~`
`AutoloadFunction('app_emailer');
app_emailer(array("setting1"=>$value1));`*/

	function app_emailer($settings = array())
		{
			register_use(__FUNCTION__);
			AutoloadFunction('create_emailer');
			return create_emailer(array("info"=>$settings));
		}
?>