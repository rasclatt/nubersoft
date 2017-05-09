<?php
	class CheckVitals
		{
			protected	$nuber;
			protected	$nubquery;
			public		$live_status;
			
			public	function __construct($nuber = false)
				{
					AutoloadFunction('nQuery');
					$this->nuber	=	$nuber;
					$this->nubquery	=	nQuery();
				}
			
			public	function SiteStatus()
				{
					// If the query engine is available
					// This is an indication that the site at least works.
					if($this->nubquery !== false) {
							// Check that the site is live
							$active	=	$this->nubquery->select(array("page_live"))->from("system_settings")->where(array("page_element"=>"site","name"=>"site_live","component"=>"site_live"))->fetch();
							
							if(isset($active[0]))
								// Return true if on false if not
								$this->live_status	=	($active[0]['page_live'] == 'on')? true:false;
							else {
									global $_errors;
									$_errors['site_live'][]	=	'Site live ineffective.';
								}
						}
					$this->live_status	=	(!isset($this->live_status))? false:$this->live_status;
					return $this;
				}
		}
?>