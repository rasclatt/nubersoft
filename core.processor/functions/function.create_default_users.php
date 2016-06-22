<?php
	function create_default_prefs($create = false)
		{
			
			AutoloadFunction('nQuery');
			$insert	=	"";
	
			$nubquery	=	nQuery();
			
			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `users`",true)->write();
					$create	=	"CREATE TABLE `users` (
  `ID` int(20) NOT NULL auto_increment,
  `unique_id` varchar(50) collate utf8_unicode_ci NOT NULL,
  `username` varchar(60) collate utf8_unicode_ci NOT NULL,
  `password` varchar(256) collate utf8_unicode_ci NOT NULL,
  `email` varchar(60) collate utf8_unicode_ci NOT NULL,
  `first_name` varchar(60) collate utf8_unicode_ci NOT NULL,
  `last_name` varchar(60) collate utf8_unicode_ci NOT NULL,
  `usergroup` int(10) NOT NULL default '2',
  `file` varchar(50) collate utf8_unicode_ci default '',
  `file_path` longtext collate utf8_unicode_ci,
  `file_name` varchar(100) collate utf8_unicode_ci default '',
  `file_size` int(20) default '0',
  `user_status` varchar(3) collate utf8_unicode_ci NOT NULL default 'on',
  `page_order` int(3) default NULL,
  `page_live` varchar(3) collate utf8_unicode_ci default NULL,
  `core_setting` int(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `username` (`username`),
  KEY `password` (`password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

					$nubquery->addCustom($create,true)->write();
				}

			// Write new settings
			$nubquery->addCustom($insert,true)->write();
		}
?>