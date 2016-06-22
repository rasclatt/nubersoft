<?php
	function create_default_menus($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery,check_empty,validate_table_column,organize,compare_table_installs');
			$create		=	(check_empty($settings,'create',true))? true:false;
			$nubquery	=	nQuery();
			$columns	=	array('unique_id', 'assoc_column', 'menuName', 'menuVal', 'restriction', 'core_setting', 'page_order', 'page_live');
			$currCols	=	array_keys(organize($nubquery->describe("dropdown_menus")->fetch(),"Field"));
			
			$col_setting	=	"`ID` int(20) NOT NULL auto_increment,
  `unique_id` varchar(100) collate utf8_unicode_ci default NULL,
  `assoc_column` varchar(60) collate utf8_unicode_ci default NULL,
  `menuName` varchar(128) collate utf8_unicode_ci default NULL,
  `menuVal` text collate utf8_unicode_ci,
  `restriction` varchar(30) collate utf8_unicode_ci default NULL,
  `core_setting` int(1) NOT NULL default '0',
  `page_order` int(3) default NULL,
  `page_live` varchar(3) collate utf8_unicode_ci default NULL,";

			compare_table_installs($col_setting,$currCols,'dropdown_menus');
				
			$insert['cols']	=	"INSERT INTO `dropdown_menus` (`unique_id`, `assoc_column`, `menuName`, `menuVal`, `restriction`, `core_setting`, `page_order`, `page_live`) VALUES";
			$insert['vals']	=	"(1, '2014071920310890000', 'head_component', 'HTML', 'html', 'NULL', 1, 0, ''),
(2, '2014072705035210000', 'column_type', 'Text Box', 'text', '', 1, 0, 'on'),
(3, '2014072705035770000', 'color', 'Gray', '#333333', '', 1, 0, 'on'),
(4, '20150109182047100000', 'color', 'Black', '#000000', NULL, 1, 0, 'on'),
(5, '20150109182045500000', 'color', 'Gray - Light', '#CCCCCC', NULL, 1, 0, 'on'),
(6, '20150109182049800000', 'column_type', 'Radio Buttons', 'radio', NULL, 1, 0, 'on'),
(7, '2014072705041030000', 'column_type', 'Text Area', 'textarea', '', 1, 0, 'on'),
(8, '20150109182050100000', 'column_type', 'File', 'file', NULL, 1, 0, 'on'),
(9, '20150109182051900000', 'column_type', 'Password', 'password', NULL, 1, 0, 'on'),
(10, '2015010918205460000', 'column_type', 'Hidden', 'hidden', NULL, 1, 0, 'on'),
(11, '20150109182053600000', 'column_type', 'Drop Down', 'select', NULL, 1, 0, 'on'),
(12, '20150109182057200000', 'column_type', 'Check Box', 'checkbox', NULL, 1, 0, 'on'),
(13, '20150109182057700000', 'component', 'Bypass Header', 'bypass_header', NULL, 1, 0, 'on'),
(14, '20150109182058400000', 'component', 'Bypass Login', 'bypass_login', NULL, 1, 0, 'on'),
(15, '20150109182059900000', 'component_type', 'image', 'image', NULL, 1, 0, 'on'),
(16, '20150112173317500000', 'component_type', 'row', 'row', NULL, 1, 0, 'on'),
(17, '2015011319131250000', 'component_type', 'code', 'code', NULL, 1, 0, 'on'),
(18, '20150113191310200000', 'component_type', 'email Form', 'form_email', NULL, 1, 0, 'on'),
(19, '20150113191309700000', 'component_type', 'container', 'div', NULL, 1, 0, 'on'),
(20, '20150113191307700000', 'component_type', 'text', 'text', NULL, 1, 0, 'on'),
(21, '20150113191832200000', 'core_setting', 'User Setting', '0', NULL, 1, 0, 'on'),
(22, '20150112183648700000', 'component_type', 'button', 'button', NULL, 1, 0, 'on'),
(23, '20150113191821700000', 'email_id', 'account', 'account', NULL, 1, 0, 'on'),
(24, '20150113164806300000', 'in_menubar', 'on', 'on', NULL, 1, 0, 'on'),
(25, '20150113191745200000', 'hidden_task', 'Form Request', 'request', NULL, 1, 0, 'on'),
(26, '20150113191737600000', 'hidden_task', 'None', '', NULL, 1, 0, 'on'),
(27, '2015011319173560000', 'login_permission', 'Super User', '0', NULL, 1, 0, 'on'),
(28, '20150113191602800000', 'login_permission', 'Admin', '1', NULL, 1, 0, 'on'),
(29, '20150112173442200000', 'menu_component', 'background-image', 'background-image', NULL, 1, 0, 'on'),
(30, '2015011217344090000', 'login_permission', 'Web User', '2', NULL, 1, 0, 'on'),
(31, '20150112173437800000', 'overflow', 'auto', 'auto', NULL, 1, 0, 'on'),
(32, '20150112173434700000', 'in_menubar', 'off', 'off', NULL, 1, 0, 'on'),
(33, '20150112173432100000', 'login_view', 'off', 'off', 'NULL', 1, 0, 'on'),
(34, '20150112173407700000', 'hidden_task', 'Cookie', 'cookie', NULL, 1, 0, 'on'),
(35, '20150112173357100000', 'overflow', 'hidden', 'hidden', NULL, 1, 0, 'on'),
(36, '2014050714455920000', 'login_view', 'on', 'on', '', 1, 0, 'on'),
(37, '20150112161848400000', 'page_live', 'off', '', NULL, 1, 0, 'on'),
(38, '20150112161849400000', 'page_live', 'on', 'on', NULL, 1, 0, 'on'),
(39, '20150112173355100000', 'menu_component', 'background-color', 'background-color', NULL, 1, 0, 'on'),
(40, '2015011217335070000', 'restriction', 'Admin', '1', NULL, 1, 0, 'on'),
(41, '20150112173352300000', 'restriction', 'Superuser', '0', 'NULL', 1, 0, 'on'),
(42, '20150112173347500000', 'restriction', 'Basic', '2', NULL, 1, 0, 'on'),
(43, '20150112173345200000', 'section', '5', '5', NULL, 1, 0, 'on'),
(44, '2014050714455760000', 'session_status', 'on', 'on', NULL, 1, 0, 'on'),
(45, '20150109182126600000', 'text_align', 'right', 'right', NULL, 1, 0, 'on'),
(46, '20150109182122800000', 'row_type', 'button', 'button', NULL, 1, 0, 'on'),
(47, '2014072420041510000', 'template', 'Default', 'template/default', NULL, 1, 0, 'on'),
(48, '20150109182120900000', 'session_status', 'off', 'off', NULL, 1, 0, 'on'),
(49, '2015010918211780000', 'text_align', 'left', 'left', NULL, 1, 0, 'on'),
(50, '20150109182115800000', 'toggle', 'off', 'off', NULL, 1, 0, 'on'),
(51, '2014050714455280000', 'toggle', 'on', 'on', '', 1, 0, 'on'),
(52, '20150109182114900000', 'text_shadow', 'None', '', NULL, 1, 0, 'on'),
(53, '20150109182112800000', 'text_align', 'None', '', NULL, 1, 0, 'on'),
(54, '20150109182107400000', 'usergroup', 'web', '3', '2', 1, 0, 'on'),
(55, '20150109182106500000', 'user_status', 'off', 'off', NULL, 1, 0, 'on'),
(56, '20150109182104400000', 'text_shadow', '2,2,3 Black', '2px 2px 3px #000', NULL, 1, 0, 'on'),
(57, '2014050714454930000', 'user_status', 'on', 'on', NULL, 1, 0, 'on'),
(58, '2015011217350480000', 'box_shadow', 'none', '', NULL, 1, 0, 'on'),
(59, '2015011217350550000', 'usergroup', 'superuser', '1', '0', 1, 0, 'on'),
(60, '20150112173510900000', 'usergroup', 'System', '1000', NULL, 1, 0, 'on'),
(61, '20150112173512300000', 'background_color', 'Dark Grey', '#B3B3B3', NULL, 1, 0, 'on'),
(62, '20150113191727500000', 'box_shadow', 'outer', '3px 3px 6px #000', NULL, 1, 0, 'on'),
(63, '2015011319172470000', 'background_color', 'Black', '#000000', NULL, 1, 0, 'on'),
(64, '20150113191721700000', 'background_color', 'Darker Gray', '#7a7a7c', NULL, 1, 0, 'on'),
(65, '20150113191716200000', 'background_color', 'Ultra-Dark Grey', '#333333', NULL, 1, 0, 'on'),
(66, '2015011319171370000', 'background_color', 'Red', '#A9010A', NULL, 1, 0, 'on'),
(67, '2015011319171140000', 'background_color', 'White', '#FFFFFF', NULL, 1, 0, 'on'),
(68, '2015011319170830000', 'auto_cache', 'off', 'off', 'NULL', 1, 0, 'on'),
(69, '20150113191701200000', 'background_color', 'None', '', NULL, 1, 0, 'on'),
(70, '20150113191705300000', 'admin_tag', 'Green', 'green', NULL, 1, 0, 'on'),
(71, '2014050714454510000', 'auto_cache', 'on', 'on', '', 1, 0, 'on'),
(72, '2015011319163550000', 'allowed_request', 'GET', 'g', '0', 1, 0, 'on'),
(73, '2015011319161710000', 'admin_tag', 'Bue', 'blue', NULL, 1, 0, 'on'),
(74, '2015011319165480000', 'admin_tag', 'None', '', NULL, 1, 0, 'on'),
(75, '20150113191613900000', 'allowed_request', 'POST', 'p', '0', 1, 0, 'on'),
(76, '20150112173308800000', 'admin_tag', 'Orange', 'orange', NULL, 1, 0, 'on'),
(77, '2015011217315290000', 'admin_tag', 'Red', 'red', NULL, 1, 0, 'on'),
(78, '20150112173153200000', 'admin_lock', 'off', '', NULL, 1, 0, 'on'),
(79, '2014050714454140000', 'admin_lock', 'on', 'on', '', 1, 0, 'on'),
(80, '20150112173513800000', 'background_color', 'Light Gray', '#D8D8D8', NULL, 1, 0, 'on'),
(81, '2015011319182920000', 'display', 'block', 'block', NULL, 1, 0, 'on'),
(82, '20150113191815400000', 'footer_component', 'Social Media', 'social_media', NULL, 1, 0, 'on'),
(83, '2015011319181240000', 'head_component', 'meta', 'meta', NULL, 1, 0, 'on'),
(84, '20150113191805300000', 'hidden_task', 'Session', 'session', NULL, 1, 0, 'on'),
(85, '20150113191801800000', 'footer_component', 'Legal Copy', 'legal_copy', NULL, 1, 0, 'on'),
(86, '20150113191752600000', 'head_component', 'head', 'head', NULL, 1, 0, 'on'),
(87, '20150113191749700000', 'head_component', 'doctype', 'doctype', NULL, 1, 0, 'on'),
(88, '2015010918211010000', 'text_align', 'center', 'center', NULL, 1, 0, 'on'),
(89, '2015010918210920000', 'user_access', 'disallow', '0', NULL, 1, 0, 'on'),
(90, '20150112173459400000', 'toggle', 'exception', 'exception', NULL, 1, 0, 'on'),
(91, '20150112173453900000', 'user_access', 'allow', '1', NULL, 1, 0, 'on'),
(92, '20150112173502200000', 'usergroup', 'none', '', NULL, 1, 0, 'on'),
(93, '201501121735081000', 'usergroup', 'admin', '2', '1', 1, 0, 'on'),
(94, '20150115133127300000', 'super', '', '', NULL, 1, NULL, 'NUL'),
(95, '2015011217315050000', 'admin_tag', 'Yellow', 'yellow', NULL, 1, 0, 'on'),
(96, '2015011217312540000', 'activation_stage', 'interface', 'gui', NULL, 1, 0, 'on'),
(97, '20150112173147700000', 'activated', 'yes', 'on', NULL, 1, 0, 'on'),
(98, '2015011217314680000', '_position', 'absolute', 'absolute', NULL, 1, 0, 'on'),
(99, '20150112173144700000', 'activation_stage', 'Process Form', 'process', NULL, 1, 0, 'on'),
(100, '2015011217314340000', 'activated', 'off', '', NULL, 1, 0, 'on'),
(101, '20150112173141100000', '_float', 'left', 'left', NULL, 1, 0, 'on'),
(102, '20150112173135600000', '_float', 'right', 'right', NULL, 1, 0, 'on'),
(103, '20150112173043200000', 'component', 'Bypass Menu', 'bypass_menu', NULL, 1, 0, 'on'),
(104, '20150112173034700000', 'component', 'Bypass Footer', 'bypass_footer', NULL, 1, 0, 'on'),
(105, '20150112173031100000', '_float', 'None', '', NULL, 1, 0, 'on'),
(106, '20150112173025100000', 'footer_component', 'Design Content', 'content', NULL, 1, 0, 'on'),
(107, '2015011217291460000', 'display', 'inline-block', 'inline-block', NULL, 1, 0, 'on'),
(108, '20150112172912600000', 'email_id', 'default', 'default', NULL, 1, 0, 'on'),
(109, '20150112172909800000', 'core_setting', 'System Setting', '1', NULL, 1, 0, 'on'),
(110, '20150112172907600000', 'color', 'Red', '#A9010A', NULL, 1, 0, 'on'),
(111, '20150112172904600000', 'color', 'Gray - Medium', '#666666', NULL, 1, 0, 'on'),
(112, '2015011217285810000', 'box_shadow', 'inner', 'inset 0 0 5px #000', NULL, 1, 0, 'on'),
(113, '2015011217285470000', '_position', 'fixed', 'fixed', NULL, 1, 0, 'on'),
(114, '20150112172840300000', '_position', 'none', '', NULL, 1, 0, 'on'),
(115, '20150108035051600000', '_position', 'relative', 'relative', NULL, 1, 0, 'on'),
(116, '20150108034851300000', '_position', 'static', 'static', NULL, 1, 0, 'on'),
(117, '20150115133127300000', 'super', '', '', NULL, 1, 0, 'NUL'),
(118, '2015011314144760000', 'page_element', 'Site', 'site', NULL, 1, 0, 'on'),
(119, '20150113141431400000', 'page_element', 'Head', 'head', NULL, 1, 0, 'on'),
(120, '2015011314141140000', 'page_element', 'Foot', 'foot', NULL, 1, 0, 'on'),
(121, '20150112161844200000', 'page_live', 'Off (Explicit)', 'off', NULL, 1, 0, 'on'),
(122, '2014072021161590000', 'create_folder', 'Off', '', 'NULL', 1, 0, 'on'),
(123, '2014072021160630000', 'create_folder', 'On', 'on', '', 1, 0, 'on'),
(124, '20150205160323263133', 'is_admin', 'AdminTools', '1', 'NULL', 1, 0, 'on'),
(125, '20150205160340553079', 'is_admin', 'Display', '0', 'NULL', 1, 0, 'on'),
(126, '201503311815154930551173168', 'return_copy', 'On', 'on', 'NULL', 0, 0, 'on'),
(127, '201503311815253062551173456', 'return_copy', 'Off', 'off', 'NULL', 0, 0, 'on'),
(128, '2015040215510534695519923', 'map_input', 'Use Textbox', 'textarea', 'NULL', 1, 0, 'on'),
(129, '2015040215513553805519765', 'map_input', 'Use Text Line', 'text', 'NULL', 1, 0, 'on'),
(130, '2015040319233025735512026101', 'map_input', 'Use Dropdown', 'select', 'NULL', 1, 0, 'on'),
(131, '201505291400178924556836103547', 'template', 'New', 'client_assets/template/nUberSoft', NULL, 0, 1, 'on')";

			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `dropdown_menus`",true)->write();
					$create	=	"CREATE TABLE `dropdown_menus` (
  $col_setting
  PRIMARY KEY  (`ID`),
  KEY `assoc_column` (`assoc_column`),
  KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
					
					$insert['vals']	=	preg_replace('/\([0-9]{1,}\,\s/', "(", $insert['vals']);
					
					$nubquery->addCustom($create,true)->write();
					$sql	=	$insert['cols'].$insert['vals'];
					// Delete from settings
					$nubquery->delete()->from("dropdown_menus")->write();
					// Write new settings
					$nubquery->addCustom($sql,true)->write();
				}
			
		}
?>