<?php
function password_reset_msg()
	{
		$incidentals	=	(!empty(NubeData::$incidentals))? Safe::to_array(NubeData::$incidentals): array();
		$change			=	(!empty($incidentals['pass_match'][0]['success']));
		
		if(!$change)
			return (!empty($incidentals['pass_match'][0]['msg']))? $incidentals['pass_match'][0]['msg'] : false;
		
		return false;
	}