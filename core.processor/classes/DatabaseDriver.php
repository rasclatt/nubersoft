<?php
/*Title: DatabaseDriver*/
/*Description: This class sets the standard for required base database connection.*/
	interface	DatabaseDriver
		{
			public	static	function connect();
		}