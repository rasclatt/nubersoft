<?php

$create[]	=	"
CREATE TABLE `components` (
  `ID` int(11) NOT NULL,
  `unique_id` varchar(100) COLLATE utf8_bin DEFAULT '',
  `ref_page` varchar(255) COLLATE utf8_bin DEFAULT '',
  `parent_id` varchar(100) COLLATE utf8_bin DEFAULT '',
  `ref_anchor` varchar(255) COLLATE utf8_bin DEFAULT '',
  `title` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `category_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `component_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `file` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `file_size` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `file_path` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `file_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_notes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `usergroup` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `group_id` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `page_order` int(10) DEFAULT '1',
  `page_live` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

$create[]	=	"
CREATE TABLE `component_locales` (
  `ID` int(20) NOT NULL,
  `unique_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `comp_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `locale_abbr` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `page_live` varchar(4) COLLATE utf8_unicode_ci DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$create[]	=	"
CREATE TABLE `dropdown_menus` (
  `ID` int(11) NOT NULL,
  `unique_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `assoc_column` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuName` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menuVal` text COLLATE utf8_unicode_ci,
  `page_order` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `restriction` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_live` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$insert[]	=	preg_replace('/\([\d]+,/', '(',"
INSERT INTO `dropdown_menus` (`unique_id`, `assoc_column`, `menuName`, `menuVal`, `page_order`, `restriction`, `page_live`) VALUES
(1, '2014071920310890000', 'head_component', 'HTML', 'html', '', 'NULL', ''),
(2, '2014072705035210000', 'column_type', 'Text Box', 'text', '', '', 'on'),
(3, '2014072705035770000', 'color', 'Gray', '#333333', '', '', 'on'),
(4, '20150109182047100000', 'color', 'Black', '#000000', '', '', 'on'),
(5, '20150109182045500000', 'color', 'Gray - Light', '#CCCCCC', '', '', 'on'),
(6, '20150109182049800000', 'column_type', 'Radio Buttons', 'radio', '', '', 'on'),
(7, '2014072705041030000', 'column_type', 'Text Area', 'textarea', '', '', 'on'),
(8, '20150109182050100000', 'column_type', 'File', 'file', '', '', 'on'),
(9, '20150109182051900000', 'column_type', 'Password', 'password', '', '', 'on'),
(10, '2015010918205460000', 'column_type', 'Hidden', 'hidden', '', '', 'on'),
(11, '20150109182053600000', 'column_type', 'Drop Down', 'select', '', '', 'on'),
(12, '20150109182057200000', 'column_type', 'Check Box', 'checkbox', '', '', 'on'),
(13, '20150109182057700000', 'component', 'Bypass Header', 'bypass_header', '', '', 'on'),
(14, '20150109182058400000', 'component', 'Bypass Login', 'bypass_login', '', '', 'on'),
(15, '20150109182059900000', 'component_type', 'Image', 'image', '', '', 'on'),
(17, '2015011319131250000', 'component_type', 'Code', 'code', '', '', 'on'),
(18, '20150113191310200000', 'component_type', 'Contact Form', 'contact_form', '', '', 'on'),
(19, '20150113191309700000', 'component_type', 'container', 'div', '', '', 'on'),
(21, '20150113191832200000', 'core_setting', 'User Setting', '0', '', '', 'on'),
(23, '20150113191821700000', 'email_id', 'account', 'account', '', '', 'on'),
(24, '20150113164806300000', 'in_menubar', 'on', 'on', '', '', 'on'),
(25, '20150113191745200000', 'hidden_task', 'Form Request', 'request', '', '', 'on'),
(26, '20150113191737600000', 'hidden_task', 'None', '', '', '', 'on'),
(27, '2015011319173560000', 'login_permission', 'Super User', '1', '', '0', 'on'),
(28, '20150113191602800000', 'login_permission', 'Admin', '2', '', '0', 'on'),
(29, '20150112173442200000', 'menu_component', 'background-image', 'background-image', '', '', 'on'),
(30, '2015011217344090000', 'login_permission', 'Web User', '3', '', '0', 'on'),
(31, '20150112173437800000', 'overflow', 'auto', 'auto', '', '', 'on'),
(32, '20150112173434700000', 'in_menubar', 'off', 'off', '', '', 'on'),
(33, '20150112173432100000', 'login_view', 'off', 'off', '', 'NULL', 'on'),
(34, '20150112173407700000', 'hidden_task', 'Cookie', 'cookie', '', '', 'on'),
(35, '20150112173357100000', 'overflow', 'hidden', 'hidden', '', '', 'on'),
(36, '2014050714455920000', 'login_view', 'on', 'on', '', '', 'on'),
(37, '20150112161848400000', 'page_live', 'off', '', '', '', 'on'),
(38, '20150112161849400000', 'page_live', 'on', 'on', '', '', 'on'),
(39, '20150112173355100000', 'menu_component', 'background-color', 'background-color', '', '', 'on'),
(40, '2015011217335070000', 'restriction', 'Admin', '1', '', '', 'on'),
(41, '20150112173352300000', 'restriction', 'Superuser', '0', '', 'NULL', 'on'),
(42, '20150112173347500000', 'restriction', 'Basic', '2', '', '', 'on'),
(43, '20150112173345200000', 'section', '5', '5', '', '', 'on'),
(44, '2014050714455760000', 'session_status', 'on', 'on', '', '', 'on'),
(45, '20150109182126600000', 'text_align', 'right', 'right', '', '', 'on'),
(46, '20150109182122800000', 'row_type', 'button', 'button', '', '', 'on'),
(47, '2014072420041510000', 'template', 'Default', '/core/template/default/', '', '0', 'on'),
(48, '20150109182120900000', 'session_status', 'off', 'off', '', '', 'on'),
(49, '2015010918211780000', 'text_align', 'left', 'left', '', '', 'on'),
(50, '20150109182115800000', 'toggle', 'off', 'off', '', '', 'on'),
(51, '2014050714455280000', 'toggle', 'on', 'on', '', '', 'on'),
(52, '20150109182114900000', 'text_shadow', 'None', '', '', '', 'on'),
(53, '20150109182112800000', 'text_align', 'None', '', '', '', 'on'),
(54, '20150109182107400000', 'usergroup', 'Web User', 'NBR_WEB', '1', '2', 'on'),
(55, '20150109182106500000', 'user_status', 'off', 'off', '', '', 'on'),
(56, '20150109182104400000', 'text_shadow', '2,2,3 Black', '2px 2px 3px #000', '', '', 'on'),
(57, '2014050714454930000', 'user_status', 'on', 'on', '', '', 'on'),
(58, '2015011217350480000', 'box_shadow', 'none', '', '', '', 'on'),
(59, '2015011217350550000', 'usergroup', 'Super User', 'NBR_SUPERUSER', '3', '0', 'on'),
(60, '20150112173512300000', 'background_color', 'Dark Grey', '#B3B3B3', '', '', 'on'),
(61, '20150113191727500000', 'box_shadow', 'outer', '3px 3px 6px #000', '', '', 'on'),
(62, '2015011319172470000', 'background_color', 'Black', '#000000', '', '', 'on'),
(63, '20150113191721700000', 'background_color', 'Darker Gray', '#7a7a7c', '', '', 'on'),
(64, '20150113191716200000', 'background_color', 'Ultra-Dark Grey', '#333333', '', '', 'on'),
(65, '2015011319171370000', 'background_color', 'Red', '#A9010A', '', '', 'on'),
(66, '2015011319171140000', 'background_color', 'White', '#FFFFFF', '', '', 'on'),
(67, '2015011319170830000', 'auto_cache', 'off', 'off', '', 'NULL', 'on'),
(68, '20150113191701200000', 'background_color', 'None', '', '', '', 'on'),
(69, '20150113191705300000', 'admin_tag', 'Green', 'green', '', '', 'on'),
(70, '2014050714454510000', 'auto_cache', 'on', 'on', '', '', 'on'),
(71, '2015011319163550000', 'allowed_request', 'GET', 'g', '', '0', 'on'),
(72, '2015011319161710000', 'admin_tag', 'Bue', 'blue', '', '', 'on'),
(73, '2015011319165480000', 'admin_tag', 'None', '', '', '', 'on'),
(74, '20150113191613900000', 'allowed_request', 'POST', 'p', '', '0', 'on'),
(75, '20150112173308800000', 'admin_tag', 'Orange', 'orange', '', '', 'on'),
(76, '2015011217315290000', 'admin_tag', 'Red', 'red', '', '', 'on'),
(77, '20150112173153200000', 'admin_lock', 'off', '', '', '', 'on'),
(78, '2014050714454140000', 'admin_lock', 'on', 'on', '', '', 'on'),
(79, '20150112173513800000', 'background_color', 'Light Gray', '#D8D8D8', '', '', 'on'),
(80, '2015011319182920000', 'display', 'block', 'block', '', '', 'on'),
(81, '20150113191815400000', 'footer_component', 'Social Media', 'social_media', '', '', 'on'),
(82, '2015011319181240000', 'head_component', 'meta', 'meta', '', '', 'on'),
(83, '20150113191805300000', 'hidden_task', 'Session', 'session', '', '', 'on'),
(84, '20150113191801800000', 'footer_component', 'Legal Copy', 'legal_copy', '', '', 'on'),
(85, '20150113191752600000', 'head_component', 'head', 'head', '', '', 'on'),
(86, '20150113191749700000', 'head_component', 'doctype', 'doctype', '', '', 'on'),
(87, '2015010918211010000', 'text_align', 'center', 'center', '', '', 'on'),
(88, '2015010918210920000', 'user_access', 'disallow', '0', '', '', 'on'),
(89, '20150112173459400000', 'toggle', 'exception', 'exception', '', '', 'on'),
(90, '20150112173453900000', 'user_access', 'allow', '1', '', '', 'on'),
(91, '201501121735081000', 'usergroup', 'Admin User', 'NBR_ADMIN', '2', '0', 'on'),
(92, '20150115133127300000', 'super', '', '', '', '', 'NUL'),
(93, '2015011217315050000', 'admin_tag', 'Yellow', 'yellow', '', '', 'on'),
(94, '2015011217312540000', 'activation_stage', 'interface', 'gui', '', '', 'on'),
(95, '20150112173147700000', 'activated', 'yes', 'on', '', '', 'on'),
(96, '2015011217314680000', '_position', 'absolute', 'absolute', '', '', 'on'),
(97, '20150112173144700000', 'activation_stage', 'Process Form', 'process', '', '', 'on'),
(98, '2015011217314340000', 'activated', 'off', '', '', '', 'on'),
(99, '20150112173141100000', '_float', 'left', 'left', '', '', 'on'),
(100, '20150112173135600000', '_float', 'right', 'right', '', '', 'on'),
(101, '20150112173043200000', 'component', 'Bypass Menu', 'bypass_menu', '', '', 'on'),
(102, '20150112173034700000', 'component', 'Bypass Footer', 'bypass_footer', '', '', 'on'),
(103, '20150112173031100000', '_float', 'None', '', '', '', 'on'),
(104, '20150112173025100000', 'footer_component', 'Design Content', 'content', '', '', 'on'),
(105, '2015011217291460000', 'display', 'inline-block', 'inline-block', '', '', 'on'),
(106, '20150112172912600000', 'email_id', 'default', 'default', '', '', 'on'),
(107, '20150112172909800000', 'core_setting', 'System Setting', '1', '', '', 'on'),
(108, '20150112172907600000', 'color', 'Red', '#A9010A', '', '', 'on'),
(109, '20150112172904600000', 'color', 'Gray - Medium', '#666666', '', '', 'on'),
(110, '2015011217285810000', 'box_shadow', 'inner', 'inset 0 0 5px #000', '', '', 'on'),
(111, '2015011217285470000', '_position', 'fixed', 'fixed', '', '', 'on'),
(112, '20150112172840300000', '_position', 'none', '', '', '', 'on'),
(113, '20150108035051600000', '_position', 'relative', 'relative', '', '', 'on'),
(114, '20150108034851300000', '_position', 'static', 'static', '', '', 'on'),
(115, '20150115133127300000', 'super', '', '', '', '', 'NUL'),
(116, '2015011314144760000', 'page_element', 'Site', 'settings_site', '', '', 'on'),
(117, '20150113141431400000', 'page_element', 'Head', 'settings_head', '', '', 'on'),
(118, '2015011314141140000', 'page_element', 'Foot', 'settings_foot', '', '', 'on'),
(119, '20150112161844200000', 'page_live', 'Off (Explicit)', 'off', '', '', 'on'),
(120, '2014072021161590000', 'create_folder', 'Off', '', '', 'NULL', 'on'),
(121, '2014072021160630000', 'create_folder', 'On', 'on', '', '', 'on'),
(122, '20150205160323263133', 'is_admin', 'AdminTools', '1', '', 'NULL', 'on'),
(123, '20150205160340553079', 'is_admin', 'Display', '0', '', '0', 'on'),
(124, '201503311815154930551173168', 'return_copy', 'On', 'on', '', 'NULL', 'on'),
(125, '201503311815253062551173456', 'return_copy', 'Off', 'off', '', 'NULL', 'on'),
(126, '2015040215510534695519923', 'map_input', 'Use Textbox', 'textarea', '', 'NULL', 'on'),
(127, '2015040215513553805519765', 'map_input', 'Use Text Line', 'text', '', 'NULL', 'on'),
(128, '2015040319233025735512026101', 'map_input', 'Use Dropdown', 'select', '', 'NULL', 'on'),
(129, '201508232215545180558848252', 'is_admin', 'Home', '2', '', '', 'on'),
(131, '20161201130353584066097169', 'downloadable', 'No', 'off', '', '0', 'on'),
(132, '20161201130406584066165', 'downloadable', 'Yes', 'on', '', '0', 'on'),
(133, '201612011310335840679922', 'editable', 'No', 'off', '', '0', 'on'),
(134, '20161201131045584067572', 'editable', 'Yes', 'on', '', '0', ''),
(135, '2016120113235058406685381', 'readable', 'No', 'off', '', '', 'on'),
(136, '201612011324125840670479', 'readable', 'Yes', 'on', '', '', 'on'),
(137, '2016120713382758485723817', 'directory_protection', 'Execute Only', 'server_r', NULL, NULL, 'on'),
(138, '201612071338525848573714', 'directory_protection', 'Browser Accessable', 'browser_rw', '', '0', 'on'),
(139, '201706291936075955879921', 'locale_abbr', 'United States', 'USA', '1', NULL, 'on'),
(140, '2017062919355059558626', 'locale_abbr', 'Canada', 'CAN', '2', NULL, 'on'),
(141, '201707201632125971379168', 'template', 'Personal', '/client/template/personal/', NULL, NULL, 'on'),
(142, '201810301012013521452', 'usergroup', 'Select', NULL, '0', NULL, 'on'),
(143, '2018103010245612345', 'component_type', 'Login Form', 'login', NULL, NULL, 'on'),
(144, '2018103119131250000', 'component_type', 'Select', 'code', '', '', 'on');");

$create[]	=	"
CREATE TABLE `emailer` (
  `ID` int(20) NOT NULL,
  `unique_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `content_back` longtext COLLATE utf8_unicode_ci NOT NULL,
  `return_copy` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `return_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `return_response` text COLLATE utf8_unicode_ci NOT NULL,
  `email_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `page_live` varchar(3) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$insert[]	=	preg_replace('/\([\d]+,/', '(', "
INSERT INTO `emailer` (`unique_id`, `content`, `content_back`, `return_copy`, `return_address`, `return_response`, `email_id`, `page_live`) VALUES
(5, '2013091712383917660', '&lt;html&gt;\n&lt;title&gt;Online Submission Form&lt;/title&gt;\n&lt;head&gt;\n&lt;link rel=&quot;stylesheet&quot; href=&quot;http://~SERVER::[HTTP_HOST]~/css/contrabrand.css&quot; type=&quot;text/css&quot; /&gt;\n&lt;link rel=&quot;stylesheet&quot; href=&quot;http://~SERVER::[HTTP_HOST]~/css/default.css&quot; type=&quot;text/css&quot; /&gt;\n&lt;style&gt;\nbody {\n	background-color: #EBEBEB;\n}\n#wrapper {\n	width:800px;\n	margin: 3% auto 60px auto;\n}\n.head_container	{\n	background-image: url(http://~SERVER::[HTTP_HOST]~/core.processor/images/email/background.jpg);\n	background-repeat: no-repeat;\n	display: block;\n	width: 780px;\n	height: 136px;\n	padding: 10px;\n	border-top-left-radius: 6px;\n	border-top-right-radius: 6px;\n}\n.l-hand-size	{\n	width: 380px;\n	display: inline-block;\n	float: left;\n	clear: none;\n}\np.header-h1	{\n	display: inline-block;\n	color: #FFFFFF;\n	text-shadow: 2px 2px 2px #333333;\n	font-size: 35px;\n	float: left;\n	clear: none;\n	margin: 0;\n	padding: 10px;\n}\na.login-button:link,\na.login-button:visited	{\n	display: inline-block;\n	float: left;\n	clear: left;\n	margin: 10;\n}\n.r-hand-size	{\n	width: 380px;\n	display: inline-block;\n	float: rigth;\n	clear: none;\n	text-align: right;\n}\nimg.default-logo	{\n	display: block;\n	float: right;\n}\n.body-content	{\n	width: 738px;\n	display: inline-block;\n	float: left;\n	clear: left;\n	padding: 30px;\n	border-left: 1px solid #888888;\n	border-right: 1px solid #888888;\n	border-bottom: 1px solid #888888;\n	border-radius-bottom: 15px;\n	margin-bottom: 30px;\n	background-color: #FFFFFF;\n	border-bottom-left-radius: 6px;\n	border-bottom-right-radius: 6px;\n}\n&lt;/style&gt;\n&lt;/head&gt;\n&lt;body&gt;\n&lt;div id=&quot;wrapper&quot;&gt;\n	&lt;div class=&quot;head_container&quot;&gt;\n		&lt;div class=&quot;l-hand-side&quot;&gt;\n			&lt;p class=&quot;header-h1&quot;&gt;Account Update&lt;/p&gt;\n			&lt;a class=&quot;formLinkButton login-button&quot; href=&quot;http://~SERVER::[HTTP_HOST]~&quot;&gt;Login&lt;/a&gt;\n		&lt;/div&gt;\n		&lt;div class=&quot;r-hand-size&quot;&gt;\n			&lt;img src=&quot;http://~SERVER::[HTTP_HOST]~/client_assets/images/logo/default.png&quot; class=&quot;default-logo&quot; /&gt;\n		&lt;/div&gt;\n	&lt;/div&gt;\n	&lt;div class=&quot;body-content&quot;&gt;\n		&lt;h2&gt;Automated Message for ~SERVER::[HTTP_HOST]~:&lt;/h2&gt;\n		&lt;p&gt;~POST::[question]~&lt;/p&gt;\n		&lt;p&gt;&lt;span style=&quot;font-size: 10px; color: #888888;&quot;&gt;This message is automated and is only sent to you when an update has been made. You will not be sent spam from this system. Please contact http://~SERVER::[HTTP_HOST]~ if you feel you have received this in error.&lt;/span&gt;&lt;/p&gt;\n	&lt;/div&gt;\n&lt;/div&gt;\n&lt;/body&gt;\n&lt;/html&gt;', '', 'on', 'rasclatt@me.com', '&lt;h3&gt;Thank you for your interest in our company.&lt;/h3&gt;', 'default', 'on');");

$create[]	=	"
CREATE TABLE `file_activity` (
  `ID` int(20) NOT NULL,
  `unique_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `ip_address` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `action` varchar(20) COLLATE utf8_unicode_ci DEFAULT '',
  `file_id` varchar(30) COLLATE utf8_unicode_ci DEFAULT '',
  `timestamp` varchar(20) COLLATE utf8_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$create[]	=	"
CREATE TABLE `form_builder` (
  `ID` int(20) NOT NULL,
  `unique_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `column_type` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `column_name` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_setting` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `restriction` varchar(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_order` int(3) DEFAULT NULL,
  `page_live` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$insert[]	=	preg_replace('/\([\d]+,/', '(', "
INSERT INTO `form_builder` (`unique_id`, `column_type`, `column_name`, `size`, `default_setting`, `restriction`, `page_order`, `page_live`) VALUES
(91, '20150109190421300000', 'text', 'title', '', '', '0', 0, 'on'),
(92, '20150109190413900000', 'text', 'width', '', '', '0', 0, 'on'),
(93, '20150113135135600000', 'select', 'page_element', '', '', '', 0, 'on'),
(94, '20150109190406800000', 'select', 'toggle', '', '', '', 0, 'on'),
(95, '2014072021153410000', 'select', 'create_folder', '', '', 'NULL', 0, 'on'),
(96, '20150109190744400000', 'select', 'text_shadow', '', '', '', 0, 'on'),
(97, '20150109190742800000', 'text', 'min_width', 'style=&quot;width: 30px;&quot;', '', '0', 0, 'on'),
(98, '20150109190741400000', 'text', 'min_height', 'style=&quot;width: 30px;&quot;', '', '0', 0, 'on'),
(99, '20150109190739700000', 'select', 'menu_component', '', '', '', 0, 'on'),
(100, '2015010919073740000', 'select', 'login_view', '', '', '', 0, 'on'),
(101, '20150109190734700000', 'select', 'login_permission', '', '', '', 0, 'on'),
(102, '20150109190647500000', 'text', 'link', '', '', '0', 0, 'on'),
(103, '20150109190635500000', 'select', 'in_menubar', '', '', '', 0, 'on'),
(104, '20150109190630400000', 'text', 'last_name', '', '', '', 0, 'on'),
(105, '20150109190632200000', 'select', 'image_corners', '', '', '', 0, 'on'),
(106, '20150109190622100000', 'select', 'image_border', '', '', '', 0, 'on'),
(107, '20150109190621600000', 'hidden', 'ID', '', '', '', 0, 'on'),
(108, '20150109190620700000', 'select', 'head_component', '', '', '', 0, 'on'),
(109, '2015010919061460000', 'hidden', 'file_size', '', '', '', 0, 'on'),
(110, '20150109190612600000', 'hidden', 'full_path', '', '', 'NULL', 0, 'on'),
(111, '20150109190611800000', 'text', 'first_name', '', '', '0', 0, 'on'),
(112, '20150109190610900000', 'text', 'font_size', '', '', '0', 0, 'on'),
(113, '20150109190609300000', 'hidden', 'file_name', '', '', '', 0, 'on'),
(114, '20150109190608800000', 'select', 'font_family', '', '', '', 0, 'on'),
(115, '20150109190607500000', 'select', 'difficulty', '', '', '', 0, 'on'),
(116, '20150109190605300000', 'text', 'file_path', '', '', '0', 0, 'on'),
(117, '2015010919060450000', 'select', 'display', '', '', '', 0, 'on'),
(118, '201501091905561000', 'hidden', 'command', '', '', '', 0, 'on'),
(119, '20150109190557800000', 'text', 'email', '', '', '0', 0, 'on'),
(120, '2015010919055370000', 'file', 'file', '', '', '', 0, 'on'),
(121, '20150109190554700000', 'select', 'core_setting', '', '', '', 0, 'on'),
(122, '20150109115820100000', 'textarea', 'description', 'style=&quot;min-width: 300px; width: 90%; min-height: 200px;&quot;', '', '', 0, 'on'),
(123, '20150109190549200000', 'text', 'border_radius', '', '', '0', 0, 'on'),
(124, '2015010911580690000', 'textarea', 'content', 'style=&quot;min-width: 300px; width: 90%; min-height: 200px;&quot; class=&quot;textarea&quot;', '', 'NULL', 0, 'on'),
(125, '2014072016145970000', 'text', 'component', '', '', '', 0, 'on'),
(126, '2014072023514730000', 'text', 'class', '', '', '', 0, 'on'),
(127, '20150109190536500000', 'select', 'column_type', '', '', '', 0, 'on'),
(128, '20150109190531300000', 'select', 'color', '', '', '', 0, 'on'),
(129, '2015010919053080000', 'select', 'component_type', '', '', '', 0, 'on'),
(130, '20150109190529100000', 'select', 'custom_components', '', '', '', 0, 'on'),
(131, '20150109190528600000', 'select', 'admin_tag', '', '', '', 0, 'on'),
(132, '2015010919052860000', 'select', 'background_color', '', '', '', 0, 'on'),
(133, '20150109190527500000', 'select', 'box_shadow', '', '', '', 0, 'on'),
(134, '20150109190526900000', 'checkbox', 'auto_fwd_post', '', '', '', 0, 'on'),
(135, '20150109190524500000', 'select', 'admin_lock', '', '', '', 0, 'on'),
(136, '2015010919052220000', 'select', 'allowed_request', '', '', '', 0, 'on'),
(137, '20150109190518600000', 'select', 'auto_cache', '', '', '', 0, 'on'),
(138, '20150109190515200000', 'text', 'a_href', '', '', '0', 0, 'on'),
(139, '201501091905142000', 'select', '_float', '', '', '', 0, 'on'),
(140, '2015010919051270000', 'select', '_position', '', '', '', 0, 'on'),
(141, '20150109190509600000', 'text', '_left', '', '', '0', 0, 'on'),
(142, '20150109190508100000', 'hidden', 'old_directory', '', '', '', 0, 'on'),
(143, '20150109190506100000', 'select', 'overflow', '', '', '', 0, 'on'),
(144, '2014072700504710000', 'text', 'page_order', 'style=&quot;max-width: 40px;&quot;', '', '0', 0, 'on'),
(145, '20150109190501300000', 'hidden', 'oldName', '', '', '', 0, 'on'),
(146, '20150109190459500000', 'password', 'password', '', '', '0', 0, 'on'),
(147, '20150109190458600000', 'hidden', 'unique_id', '', '', '', 0, 'on'),
(148, '20150109190457600000', 'select', 'restriction', '', '', '', 0, 'on'),
(149, '20150109190455100000', 'select', 'user_access', '', '', '', 0, 'on'),
(150, '20150109190454400000', 'select', 'session_status', '', '', '', 0, 'on'),
(151, '2015010919045320000', 'select', 'template', '', '', '', 0, 'on'),
(152, '20150109190452800000', 'select', 'style', '', '', '', 0, 'on'),
(153, '20150109190447100000', 'select', 'section', '', '', '', 0, 'on'),
(154, '20150109190446500000', 'select', 'user_status', '', '', '', 0, 'on'),
(155, '201501091904449000', 'text', 'username', '', '', '0', 0, 'on'),
(156, '20150109190441700000', 'select', 'usergroup', '', '', '', 0, 'on'),
(157, '2015010919042730000', 'text', 'name', '', '', '0', 0, 'on'),
(158, '20150109190433600000', 'select', 'text_align', '', '', '', 0, 'on'),
(159, '20150109115741600000', 'textarea', 'notes', 'style=&quot;height: 50px; width: 90%; border-color: #CCCCCC;&quot;', '', '', 0, 'on'),
(160, '20150205160420300000', 'select', 'is_admin', '', '', '0', 0, 'on'),
(161, '201503311814471166551157282', 'select', 'return_copy', '', '', 'NULL', 0, 'on'),
(162, '201503311816043367551149487', 'textarea', 'content_back', 'style=&quot;min-width: 300px; width: 90%; min-height: 200px;&quot; class=&quot;.textarea&quot;', '', 'NULL', 0, 'on'),
(163, '2015040118050651045516928007', 'text', 'css', ' ', '', 'NULL', 0, 'on'),
(164, '20150402154952323555196055', 'select', 'map_input', '', '', 'NULL', 0, 'on'),
(165, '20150404020249172155178910', 'textarea', 'menu_options', 'style=&quot;min-width: 300px; width: 90%; min-height: 200px;&quot; class=&quot;.textarea&quot;', '', 'NULL', 0, 'on'),
(166, '2016111815494258269660605', 'textarea', 'return_response', 'style=&quot;height: 100px; width: 300px;&quot;', '', '0', 0, 'on'),
(167, '20161120011114583138241', 'select', 'page_live', 'style=&quot;width: auto;&quot;', '', '0', 0, 'on'),
(168, '20161201130249584065955201', 'select', 'downloadable', '', '', '', 0, 'on'),
(169, '201612011309255840675573', 'select', 'editable', '', '', '', 0, 'on'),
(170, '2016120113233558406742', 'select', 'readable', '', '', '', 0, 'on'),
(171, '2016120713410958485751004', 'select', 'directory_protection', NULL, NULL, NULL, NULL, 'on'),
(172, '20170629193714595582707', 'select', 'locale_abbr', NULL, NULL, NULL, NULL, 'on');");

$create[]	=	"
CREATE TABLE `main_menus` (
  `ID` int(20) NOT NULL,
  `unique_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `parent_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `full_path` varchar(600) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menu_name` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_options` longtext COLLATE utf8_unicode_ci,
  `link` varchar(200) COLLATE utf8_unicode_ci DEFAULT '',
  `template` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'template/default',
  `use_page` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auto_cache` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off',
  `in_menubar` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off',
  `is_admin` int(1) DEFAULT '0',
  `auto_fwd` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auto_fwd_post` varchar(4) COLLATE utf8_unicode_ci DEFAULT 'off',
  `session_status` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off',
  `usergroup` varchar(30) COLLATE utf8_unicode_ci DEFAULT 'NBR_WEB',
  `page_live` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'off',
  `page_order` int(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$insert[]	=	"
INSERT INTO `main_menus` (`unique_id`, `parent_id`, `full_path`, `menu_name`, `group_id`, `page_options`, `link`, `template`, `use_page`, `auto_cache`, `in_menubar`, `is_admin`, `auto_fwd`, `auto_fwd_post`, `session_status`, `usergroup`, `page_live`, `page_order`) VALUES
('2016120214231458412266', '', '/AdminTools/', 'Nubersoft', NULL, '', 'admintools', '/core/template/default/', '', 'off', 'off', 1, '', 'off', 'on', 'NBR_ADMIN', 'on', 1),
('201612021444165841104219', '', '/', 'Home Page', NULL, '', 'home', '/core/template/default/', '', 'off', 'on', 2, '', 'off', 'off', '0', 'on', 1),
('20171226130020542864427', '', '/contact-us/', 'Contact', NULL, '', 'contact-us', '/core/template/default/', '', 'off', 'on', 0, '', 'off', 'off', 'NBR_WEB', 'on', 2),
('2018012313245356782531', '', '/login/', 'Login', NULL, '', 'login', '/core/template/default/', '', 'off', 'on', 3, '/', 'on', 'on', 'NBR_WEB', 'on', 1),
('20181106110902768054', '', '/my-account/', 'Account', NULL, NULL, 'my-account', '/core/template/default/', '', 'off', 'on', 0, '', 'off', 'on', 'NBR_WEB', 'on', 4);";

$create[]	=	"
CREATE TABLE `media` (
  `ID` bigint(50) UNSIGNED NOT NULL,
  `unique_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '',
  `usergroup` int(2) NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `file` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `file_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `file_size` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci,
  `terms_id` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `login_view` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `page_order` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `page_live` varchar(3) COLLATE utf8_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$create[]	=	"
CREATE TABLE `members_connected` (
  `ID` int(11) NOT NULL,
  `unique_id` varchar(50) DEFAULT '',
  `ip_address` varchar(24) DEFAULT '',
  `username` varchar(100) DEFAULT '',
  `domain` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$create[]	=	"
CREATE TABLE `system_settings` (
  `ID` int(20) NOT NULL,
  `category_id` varchar(50) DEFAULT NULL,
  `option_group_name` varchar(50) DEFAULT NULL,
  `option_attribute` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `usergroup` int(4) DEFAULT '1',
  `action` varchar(100) DEFAULT NULL,
  `page_live` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$insert[]	=	"
INSERT INTO `system_settings` (`category_id`, `option_group_name`, `option_attribute`, `usergroup`, `action`, `page_live`) VALUES
('header_company_logo', 'system', '/client/media/images/default/company_logo.png', 1, NULL, 'on'),
('header_html_toggle', 'system', 'off', 1, NULL, 'on'),
('header_html', 'system', '&lt;div class=&quot;span-3 col-count-3 offset&quot;&gt;\r\n	&lt;div class=&quot;col-2&quot;&gt;\r\n		TEST IS BEST\r\n	&lt;/div&gt;\r\n&lt;/div&gt;', 1, NULL, 'on'),
('header_javascript', 'system', '$(function(){\r\n	\r\n});', 1, NULL, 'on'),
('header_styles', 'system', '', 1, NULL, 'on'),
('header_meta', 'system', '', 1, NULL, 'on'),
('footer_html_toggle', 'system', 'on', 1, NULL, 'on'),
('footer_html', 'system', '&lt;div class=&quot;col-count-3 offset&quot; style=&quot;background-color: #888; color: #333; min-height: 250px;&quot;&gt;\r\n	&lt;div class=&quot;col-2&quot;&gt;&amp;copy; ~DATE::[Y]~ nUbersoft;&lt;/div&gt;\r\n&lt;/div&gt;', 1, NULL, 'on'),
('header_company_logo_toggle', 'system', 'off', 1, NULL, 'on'),
('webmaster', 'system', 'rasclatt@me.com', 1, NULL, 'on'),
('sign_up', 'system', 'off', 1, NULL, 'on'),
('two_factor_auth', 'system', 'off', 1, NULL, 'on'),
('frontend_admin', 'system', 'off', 1, NULL, 'on'),
('maintenance_mode', 'system', 'off', 1, NULL, 'on'),
('site_live', 'system', 'on', 1, NULL, 'on'),
('template', 'system', '/client/template/rasclatt/', 1, NULL, 'on'),
('timezone', 'system', 'America/Los_Angeles', 1, NULL, 'on'),
('htaccess', 'system', '# Deny access to file extensions\r\n&lt;FilesMatch &quot;\\.(htaccess|htpasswd|ini|flag|log|sh|pref|json|txt|html|xml|zip)$&quot;&gt;\r\nOrder Allow,Deny\r\nDeny from all\r\n&lt;/FilesMatch&gt;\r\n\r\nRewriteEngine On\r\n## FORCE HTTPS -&gt; Uncommment to force ssl\r\n##RewriteCond %{SERVER_PORT} 80 \r\n##RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\r\n## Normal Rewrites\r\nRewriteCond %{REQUEST_URI} !(/$|\\.)\r\nRewriteRule (.*) %{REQUEST_URI}/ [R=301,L]\r\nRewriteCond $1 !^(index\\.php|images|robots\\.txt)\r\nRewriteCond %{REQUEST_FILENAME} !-f\r\nRewriteCond %{REQUEST_FILENAME} !-d\r\nRewriteRule ^(.*)$ /index.php?$1 [NC,QSA,L]', 1, NULL, 'on');";

$create[]	=	"
CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `unique_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `username` varchar(60) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` varchar(256) COLLATE utf8_bin NOT NULL DEFAULT '',
  `first_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address_1` text COLLATE utf8_bin,
  `address_2` text COLLATE utf8_bin,
  `city` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `state` varchar(4) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(100) COLLATE utf8_bin DEFAULT NULL,
  `postal` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  `usergroup` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT 'NBR_WEB',
  `user_status` varchar(4) COLLATE utf8_bin DEFAULT 'on',
  `file` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `file_path` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `file_name` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `reset_password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `timestamp` varchar(255) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";

$create[]	=	"
CREATE TABLE `user_roles` (
  `ID` int(20) NOT NULL,
  `user_role` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_attribute` longtext COLLATE utf8_unicode_ci,
  `user_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";

$create[]	=	"
ALTER TABLE `components`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `ref_page` (`ref_page`,`ref_anchor`,`category_id`,`component_type`,`page_live`),
  ADD KEY `title` (`title`);";
 
$create[]	=	"
ALTER TABLE `component_locales`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `comp_id` (`comp_id`,`locale_abbr`,`page_live`);";

$create[]	=	"
ALTER TABLE `dropdown_menus`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `assoc_column` (`assoc_column`),
  ADD KEY `unique_id` (`unique_id`);";

$create[]	=	"
ALTER TABLE `emailer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email_id` (`email_id`),
  ADD KEY `unique_id` (`unique_id`,`email_id`);";

$create[]	=	"
ALTER TABLE `file_activity`
  ADD PRIMARY KEY (`ID`);";

$create[]	=	"
ALTER TABLE `form_builder`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `column_type` (`column_type`,`column_name`,`page_live`);";

$create[]	=	"
ALTER TABLE `main_menus`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `parent_id` (`parent_id`,`full_path`(255),`group_id`,`link`,`page_live`);";

$create[]	=	"
ALTER TABLE `media`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `unique_id` (`unique_id`);";

$create[]	=	"
ALTER TABLE `members_connected`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD UNIQUE KEY `username` (`username`);";

$create[]	=	"
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `page_element` (`category_id`,`usergroup`,`page_live`),
  ADD KEY `action` (`action`),
  ADD KEY `option_group_name` (`option_group_name`);";

$create[]	=	"
ALTER TABLE `users`
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `password` (`password`(255)),
  ADD KEY `user_status` (`user_status`);";

$create[]	=	"
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_role` (`user_role`);";

$create[]	=	"
ALTER TABLE `components`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$create[]	=	"
ALTER TABLE `component_locales`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"
ALTER TABLE `dropdown_menus`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `emailer`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `file_activity`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"
ALTER TABLE `form_builder`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"
ALTER TABLE `main_menus`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `media`
  MODIFY `ID` bigint(50) UNSIGNED NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `members_connected`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `system_settings`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$create[]	=	"ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$create[]	=	"
ALTER TABLE `user_roles`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";