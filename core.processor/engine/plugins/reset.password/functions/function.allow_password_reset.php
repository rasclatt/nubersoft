<?php
function allow_password_reset()
	{
		$incidentals	=	(!empty(NubeData::$incidentals))? Safe::to_array(NubeData::$incidentals): array();
		$change			=	(!empty($incidentals['pass_match'][0]['success']));
		
		return $change;
	}