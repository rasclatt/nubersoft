<?php
	function LoggedIn()
		{
			return (isset($_SESSION['usergroup']));
		}