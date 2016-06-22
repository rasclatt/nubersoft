<?php
	function create_default_timestamp($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery,check_empty,validate_table_column,organize,compare_table_installs');
			$create		=	(check_empty($settings,'create',true))? true:false;
			$nubquery	=	nQuery();
			$columns	=	array('ID', 'username', 'timestamp');
			$currCols	=	array_keys(organize($nubquery->describe("members_connected")->fetch(),"Field"));
			
			$col_setting	=	"`ID` int(20) NOT NULL auto_increment,
`username` varchar(100) default NULL,
`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,";

			compare_table_installs($col_setting,$currCols,'members_connected');
				
			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `timestamp`",true)->write();
					$create	=	"CREATE TABLE IF NOT EXISTS `members_connected` (
`ID` int(20) NOT NULL auto_increment,
`username` varchar(100) default NULL,
`timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
PRIMARY KEY  (`ID`),
UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=409";;

					$nubquery->addCustom($create,true)->write();
				}
		}
?>