<?php
/*Title: DatabaseDriver*/
/*Description: This class sets the standard for required base database connection.*/

namespace Nubersoft;

interface	DatabaseDriver
	{
		public	static	function connect();
	}