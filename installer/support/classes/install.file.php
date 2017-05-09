<?php
	class	NuberStall
		{
			public	function __construct()
				{
					if(isset($_GET['install']) && $_GET['install'] == true) {
							include_once($_SERVER['DOCUMENT_ROOT'] . '/core/includes/classes/core.essentials/core.MySQL.connect.php');
							CoreMySQL::Initialize(array('host'=>'mysqlv103','dbname'=>'iwantcreativedb','username'=>'rasclatt','password'=>'RHE123sus321'),0);
							
							global $con;
							
							$test	=	$con->prepare("show tables in iwantcreativedb");
							$test->execute();
							
							while($tables = $test->fetch(PDO::FETCH_ASSOC)) {
								//	echo '<pre>';
								//	print_r($tables);
								//	echo '</pre>';
								}
						}
				}
		}
		
		$instructions	=	new NuberStall(); ?>