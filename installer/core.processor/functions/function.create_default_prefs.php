<?php
	function create_default_prefs($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery,check_empty,validate_table_column,organize,compare_table_installs');
			$create		=	(check_empty($settings,'create',true))? true:false;
			$type		=	(check_empty($settings,'type'))? $settings['type']:false;
			$nubquery	=	nQuery();
			$columns	=	array('unique_id', 'page_element', 'name', 'component', 'map_input', 'content', 'hidden_task', 'hidden_task_trigger', 'usergroup', 'page_order', 'page_live', 'core_setting');
			
			$fields		=	$nubquery->describe("system_settings")->fetch();
			
			
			if(empty($fields))
				return false;
				
			$currCols	=	array_keys(organize($fields,"Field"));
			
			$col_setting	=	"`ID` int(20) NOT NULL auto_increment,
`unique_id` varchar(100) default '',
`page_element` varchar(50) default '',
`name` varchar(100) default '',
`component` varchar(40) default '',
`map_input` varchar(20) default '',
`content` longtext character set utf8 collate utf8_unicode_ci NOT NULL,
`hidden_task` varchar(20) default '',
`hidden_task_trigger` varchar(20) default '',
`usergroup` int(4) default '1',
`page_order` int(3) NOT NULL default '1',
`page_live` varchar(3) default '',
`core_setting` int(1) NOT NULL default '0',";

			compare_table_installs($col_setting,$currCols,'system_settings');
				
			$insert['cols']	=	"INSERT INTO `system_settings` (`unique_id`, `page_element`, `name`, `component`, `map_input`, `content`, `hidden_task`, `hidden_task_trigger`, `usergroup`, `page_order`, `page_live`, `core_setting`)
VALUES";
			$insert['vals']['foot']	=	"
	('2015050514560330554912433','settings_foot','settings','','','a:5:{s:7:\"youtube\";a:2:{s:5:\"value\";s:37:\"http://www.youtube.com/user/something\";s:6:\"toggle\";s:3:\"off\";}s:8:\"facebook\";a:2:{s:5:\"value\";s:38:\"http://www.facebook.com/user/something\";s:6:\"toggle\";s:3:\"off\";}s:7:\"twitter\";a:2:{s:5:\"value\";s:27:\"http://www.twitter.com/user\";s:6:\"toggle\";s:3:\"off\";}s:9:\"pinterest\";a:2:{s:5:\"value\";s:29:\"http://www.pinterest.com/user\";s:6:\"toggle\";s:3:\"off\";}s:4:\"html\";a:2:{s:5:\"value\";s:309:\"~app::jQuery_scroll_top~\r\n	&lt;div id=&quot;footer&quot;&gt;\r\n		&lt;div id=&quot;footerContent&quot;&gt;\r\n			&lt;p style=&quot;color: #888; font-size: 14px; margin-top: 30px;&quot;&gt;&amp;reg; Copyright ~DATE::[Y]~.  All rights reserved. Today is ~DATE::[F d Y]~ PST.&lt;/p&gt;\r\n		&lt;/div&gt;\r\n	&lt;/div&gt;\";s:6:\"toggle\";s:3:\"off\";}}','','',1,1,'',0)";
			
			$insert['vals']['site']	=	"
	('2015050514143130554908878281','settings_site','settings','','','a:8:{s:5:\"login\";s:0:\"\";s:8:\"htaccess\";s:183:\"RewriteEngine On\r\nRewriteCond $1 !^(index.php|images|robots.txt)\r\nRewriteCond %{REQUEST_FILENAME} !-f\r\nRewriteCond %{REQUEST_FILENAME} !-d\r\nRewriteRule ^(.*)$ /index.php?$1 [NC,QSA,L]\";s:4:\"menu\";s:0:\"\";s:4:\"foot\";s:0:\"\";s:4:\"head\";s:0:\"\";s:8:\"timezone\";s:19:\"America/Los_Angeles\";s:7:\"sign_up\";a:1:{s:6:\"toggle\";s:3:\"off\";}s:9:\"site_live\";a:2:{s:5:\"value\";s:70:\"&lt;p&gt;This site is temporarily taking a break. BACK SOON!&lt;/p&gt;\";s:6:\"toggle\";s:3:\"off\";}}','','',1,1,'',0)";
			
			$insert['vals']['head']	=	"
	('20150505141339305549085333','settings_head','settings','','','a:7:{s:5:\"style\";s:0:\"\";s:3:\"css\";s:228:\"&lt;link rel=&quot;stylesheet&quot; href=&quot;/css/pageprefs.css&quot; /&gt;\r\n&lt;link rel=&quot;stylesheet&quot; href=&quot;/css/default.css&quot; /&gt;\r\n&lt;link rel=&quot;stylesheet&quot; href=&quot;/css/menu.css&quot; /&gt;\";s:10:\"javascript\";s:417:\"&lt;script src=&quot;http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js&quot;&gt;&lt;/script&gt;\r\n&lt;script src=&quot;http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js&quot;&gt;&lt;/script&gt;\r\n&lt;script src=&quot;http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js&quot;&gt;&lt;/script&gt;	\r\n&lt;script src=&quot;/js/onthefly.js?v=1_1&quot;&gt;&lt;/script&gt;\";s:7:\"tinymce\";a:2:{s:5:\"value\";s:2473:\"&lt;script type=&quot;text/javascript&quot; src=&quot;/js/jscripts/tiny_mce/tiny_mce.js&quot;&gt;&lt;/script&gt;\r\n&lt;script type=&quot;text/javascript&quot;&gt;\r\ntinyMCE.init({\r\n        // General options\r\n        mode : &quot;textareas&quot;,\r\n        theme : &quot;advanced&quot;,\r\n\r\n        content_css : &quot;/css/default.css&quot;,\r\n        plugins : &quot;autolink,lists,spellchecker,pagebreak,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template&quot;,\r\n\r\n        // Theme options\r\n        theme_advanced_buttons1 : &quot;bold,italic,underline,strikethrough,,justifyleft,justifycenter,justifyright,justifyfull,,styleselect,formatselect,fontselect,fontsizeselect&quot;,\r\n        theme_advanced_buttons2 : &quot;cut,copy,paste,pastetext,pasteword,,search,replace,,bullist,numlist,,outdent,indent,blockquote,,undo,redo,,link,unlink,anchor,image,cleanup,help,code,,insertdate,inserttime,preview,,forecolor,backcolor&quot;,\r\n        theme_advanced_buttons3 : &quot;tablecontrols,,hr,removeformat,visualaid,,sub,sup,,charmap,emotions,iespell,media,advhr,,print,,ltr,rtl,,fullscreen&quot;,\r\n        theme_advanced_buttons4 : &quot;insertlayer,moveforward,movebackward,absolute,,styleprops,spellchecker,,cite,abbr,acronym,del,ins,attribs,,visualchars,nonbreaking,template,blockquote,pagebreak,,insertfile,insertimage&quot;,\r\n        theme_advanced_toolbar_location : &quot;top&quot;,\r\n        theme_advanced_toolbar_align : &quot;left&quot;,\r\n        theme_advanced_statusbar_location : &quot;bottom&quot;,\r\n        theme_advanced_resizing : true,\r\n\r\n        // Skin options\r\n        skin : &quot;o2k7&quot;,\r\n        skin_variant : &quot;silver&quot;,\r\n\r\n        // Example content CSS (should be your site CSS)\r\n        content_css : &quot;css/example.css&quot;,\r\n\r\n        // Drop lists for link/image/media/template dialogs\r\n        template_external_list_url : &quot;js/template_list.js&quot;,\r\n        external_link_list_url : &quot;js/link_list.js&quot;,\r\n        external_image_list_url : &quot;js/image_list.js&quot;,\r\n        media_external_list_url : &quot;js/media_list.js&quot;,\r\n\r\n        // Replace values for the template plugin\r\n        template_replace_values : {\r\n                username : &quot;Some User&quot;,\r\n                staffid : &quot;991234&quot;\r\n        }\r\n});\r\n&lt;/script&gt;\";s:6:\"toggle\";s:3:\"off\";}s:8:\"favicons\";s:178:\"&lt;link rel=&quot;shortcut icon&quot; href=&quot;/favicon.ico&quot; type=&quot;image/x-icon&quot; /&gt;\r\n&lt;link rel=&quot;SHORTCUT ICON&quot; HREF=&quot;/favicon.png&quot;&gt;\";s:8:\"helpdesk\";s:0:\"\";s:4:\"html\";s:109:\"&lt;div style=&quot;width: 100; background-color: green;&quot;&gt;\r\n&lt;h2&gt;Tester&lt;/h2&gt;\r\n&lt;/div&gt;\";}','','',1,1,'',0)";
			
			
			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `system_settings`",true)->write();
					$create	=	"CREATE TABLE `system_settings` (
  $col_setting
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

					$nubquery->addCustom($create,true)->write();
				}
			else {
					if(isset($insert['vals'][$type])) {
							$where	=	array("name"=>"settings","page_element"=>"settings_$type");
							$sql	=	$insert['cols'].$insert['vals'][$type];
						}
					else
						$where	=	array("name"=>"settings");
					
					// Delete from settings
					$nubquery->delete()->from("system_settings")->where($where)->write();
				}
			
			$sql	=	(isset($sql))? $sql:$insert['cols'].implode(", ",$insert['vals']);
			
			// Write new settings
			$nubquery->addCustom($sql,true)->write();
		}