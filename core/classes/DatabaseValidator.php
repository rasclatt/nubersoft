<?php
/*Title: DatabaseValidator*/
/*Description: This class sets the standard for required database health. Only the CheckDatabase() method is a requirement*/
	interface DatabaseValidator
		{
			public	static	function CheckDatabase();
		}