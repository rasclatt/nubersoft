<?php
	function create_default_image_bucket($create = false)
		{
			
			AutoloadFunction('nQuery');
			$insert	=	"";
	
			$nubquery	=	nQuery();
			
			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `image_bucket`",true)->write();
					$create	=	"CREATE TABLE IF NOT EXISTS `image_bucket` (
  `ID` bigint(50) unsigned NOT NULL auto_increment,
  `unique_id` varchar(50) character set utf8 collate utf8_bin default '',
  `ref_page` varchar(30) collate utf8_unicode_ci NOT NULL,
  `usergroup` int(2) NOT NULL,
  `username` varchar(255) character set utf8 collate utf8_bin NOT NULL default '',
  `content` longtext collate utf8_unicode_ci NOT NULL,
  `file` varchar(30) collate utf8_unicode_ci NOT NULL,
  `file_path` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `file_name` varchar(255) collate utf8_unicode_ci default '',
  `file_size` varchar(20) collate utf8_unicode_ci NOT NULL,
  `notes` text collate utf8_unicode_ci NOT NULL,
  `login_view` varchar(3) collate utf8_unicode_ci NOT NULL,
  `page_order` varchar(3) collate utf8_unicode_ci NOT NULL,
  `page_live` varchar(3) collate utf8_unicode_ci default '',
  `core_setting` int(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `ref_page` (`ref_page`),
  KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

					$nubquery->addCustom($create,true)->write();
				}

			// Write new settings
			$nubquery->addCustom($insert,true)->write();
		}
?>