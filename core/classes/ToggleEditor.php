<?php
	class ToggleEditor
		{
			public	static	function Validate()
				{
					// Check if user is admin
					if(!is_admin())
						return;
					AutoloadFunction('check_empty');
					if(check_empty($_POST,"command",'toggle_set')) {
							if($_POST['toggle'] == 1) 
								$_SESSION['toggle']['edit']['type']	=	(isset($_POST['type']))? strip_tags($_POST['type']):'track';
							else
								unset($_SESSION['toggle']);
						}
				}
		}