<?php
	function is_ajax_request()
		{
			return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']));
		}