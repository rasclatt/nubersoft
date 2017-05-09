<?php
$sql	=	"CREATE TABLE IF NOT EXISTS `{$tablename}` (
  `ID` int(20) NOT NULL auto_increment,
  `unique_id` varchar(50) collate utf8_unicode_ci NOT NULL,
  `username` varchar(50) default '' NULL,
  `ip_address` varchar(50) default '' NULL,
  `action` varchar(20) default '' NULL,
  `file` varchar(50) collate utf8_unicode_ci default '',
  `file_path` longtext collate utf8_unicode_ci,
  `full_path` varchar(255) NOT NULL collate utf8_unicode_ci default '',
  `file_name` varchar(100) NOT NULL collate utf8_unicode_ci default '',
  `file_size` int(20) default '0' NULL,
  `file_mime` varchar(50) default '' NULL,
  `file_unique` varchar(30) default '' NULL,
  `download_count` int(30) default 0,
  `timestamp` varchar(20) default '' NULL,
  `terms_id` varchar(50) default '' NULL,
  `page_order` int(3) default NULL,
  `page_live` varchar(3) collate utf8_unicode_ci default NULL,
  `core_setting` int(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";