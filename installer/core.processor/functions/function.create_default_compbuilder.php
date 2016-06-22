<?php
	function create_default_compbuilder($settings = false)
		{
			register_use(__FUNCTION__);
			AutoloadFunction('nQuery,check_empty');
			$create		=	(check_empty($settings,'create',true))? true:false;
			$nubquery	=	nQuery();
			
			$insert['cols']	=	"INSERT INTO `component_builder` (`unique_id`, `component_name`, `assoc_table`, `component_value`, `variable_type`, `map_input`, `page_order`, `page_live`, `core_setting`)
VALUES";
			$insert['vals']	=	"('20150109162254800000', 'component', 'components', '_id', 'display', '', 2, 'on', 1),
('20150109162248200000', 'image_bucket', 'image_bucket', 'notes', 'display', '', 0, 'on', 1),
('20150109162241100000', 'component', 'components', 'admin_tag', 'data', '', 3, 'on', 1),
('20150109162239100000', 'main_menus', 'main_menus', 'auto_cache', 'condition', '', 11, 'on', 1),
('2014072021144570000', 'main_menus', 'main_menus', 'use_page', 'data', '', 3, 'on', 1),
('20150109172129800000', 'sub_menus', 'sub_menus', 'content', 'display', 'textarea', 1, 'on', 1),
('20150109172134900000', 'sub_menus', 'sub_menus', 'page_live', 'function', 'select', 1, 'on', 1),
('20150109172136700000', 'main_menus', 'main_menus', 'full_path', 'data', '', 0, 'on', 1),
('20150109172137400000', 'main_menus', 'main_menus', 'page_live', 'condition', '', 10, 'on', 1),
('20150109172139800000', 'main_menus', 'main_menus', 'page_order', 'condition', '', 9, 'on', 1),
('20150109162521500000', 'main_menus', 'main_menus', 'in_menubar', 'condition', '', 8, 'on', 1),
('20150109162514300000', 'main_menus', 'main_menus', 'auto_fwd_post', 'condition', '', 0, 'on', 1),
('20150109162502800000', 'main_menus', 'main_menus', 'template', 'data', '', 3, 'on', 1),
('20150109162455300000', 'main_menus', 'main_menus', 'session_status', 'condition', '', 6, 'on', 1),
('20150109162458300000', 'main_menus', 'main_menus', 'usergroup', 'condition', '', 7, 'on', 1),
('20150109162453500000', 'main_menus', 'main_menus', 'menu_name', 'display', '', 1, 'on', 1),
('20150109162450900000', 'main_menus', 'main_menus', 'link', 'data', '', 2, 'on', 1),
('20150109162448400000', 'component', 'components', 'admin_notes', 'data', '', 1, 'on', 1),
('20150109162447600000', 'main_menus', 'main_menus', 'auto_fwd', 'condition', '', 0, 'on', 1),
('2015010916244420000', 'component', 'components', 'min_width', 'css', '', 3, 'on', 1),
('20150109162441300000', 'component', 'components', 'z_index', 'css', '', 10, 'on', 1),
('201501091624407000', 'component', 'components', '_left', 'css', '', 1, 'on', 1),
('20150109162439700000', 'component', 'components', '_right', 'css', '', 1, 'on', 1),
('20150109162438500000', 'component', 'components', 'min_height', 'css', '', 3, 'on', 1),
('20150109162437400000', 'component', 'components', '_position', 'css', '', 1, 'on', 1),
('2015010916243690000', 'component', 'components', '_top', 'css', '', 1, 'on', 1),
('20150109162435700000', 'component', 'components', 'admin_lock', 'condition', '', 3, 'on', 1),
('2015010916243480000', 'component', 'components', 'email_id', 'email/button', '', 2, 'on', 1),
('2015010916243200000', 'component', 'components', '_bottom', 'css', '', 1, 'on', 1),
('20150109162431400000', 'component', 'components', 'page_live', 'condition', '', 4, 'on', 1),
('20150109162426200000', 'component', 'components', 'page_order', 'condition', '', 2, 'on', 1),
('2015010916242470000', 'component', 'components', 'file_name', 'file', '', 2, 'on', 1),
('20150109162422400000', 'component', 'components', '_float', 'css', '', 17, 'on', 1),
('20150109162418900000', 'component', 'components', 'border_radius', 'css', '', 14, 'on', 1),
('20150109162420300000', 'component', 'components', 'box_shadow', 'css', '', 15, 'on', 1),
('20150109162415400000', 'component', 'components', 'file_path', 'file', '', 1, 'on', 1),
('20150109162417600000', 'component', 'components', 'background_repeat', 'css', '', 12, 'on', 1),
('20150109162413700000', 'component', 'components', 'text_shadow', 'css', '', 16, 'on', 1),
('20150109162411800000', 'component', 'components', 'login_permission', 'condition', '', 1, 'on', 1),
('20150109162401900000', 'component', 'components', 'login_view', 'condition', '', 0, 'on', 1),
('20150109162349400000', 'component', 'components', 'file', 'file', '', 0, 'on', 1),
('2015010916234720000', 'component', 'components', 'file_size', 'file', '', 3, 'on', 1),
('20150109162345200000', 'component', 'components', 'display', 'css', '', 0, 'on', 1),
('20150109162344100000', 'component', 'components', 'background_color', 'css', '', 9, 'on', 1),
('20150109162343800000', 'component', 'components', 'border', 'css', '', 13, 'on', 1),
('2015010916234060000', 'component', 'components', 'background_position', 'css', '', 11, 'on', 1),
('20150109162337600000', 'component', 'components', 'line_height', 'css', '', 7, 'on', 1),
('20150109162336800000', 'component', 'components', 'background_image', 'css', '', 10, 'on', 1),
('20150109162335700000', 'component', 'components', 'color', 'css', '', 8, 'on', 1),
('20150109162334600000', 'component', 'components', 'text_align', 'css', '', 5, 'on', 1),
('20150109162331400000', 'component', 'components', 'width', 'css', '', 2, 'on', 1),
('201501091623300000', 'component', 'components', 'font_size', 'css', '', 6, 'on', 1),
('2015010916232970000', 'component', 'components', 'content', 'display', '', 0, 'on', 1),
('20150109162326100000', 'component', 'components', 'margin', 'css', '', 1, 'on', 1),
('20150109162327800000', 'component', 'components', 'height', 'css', '', 3, 'on', 1),
('20150109162323300000', 'component', 'components', 'overflow', 'css', '', 4, 'on', 1),
('20150109162321400000', 'component', 'components', 'a_href', 'email/button', '', 2, 'on', 1),
('2015010916232090000', 'component', 'components', 'component_type', 'data', '', 0, 'on', 1),
('20150109162319400000', 'component', 'components', 'padding', 'css', '', 1, 'on', 1),
('2015010916231770000', 'image_bucket', 'image_bucket', 'file_name', 'file', '', 2, 'on', 1),
('20150109162314400000', 'image_bucket', 'image_bucket', 'background_color', 'css', '', 0, 'on', 1),
('2015010916231290000', 'image_bucket', 'image_bucket', 'file_size', 'file', '', 3, 'on', 1),
('2015010916231140000', 'component', 'components', 'class', 'display', '', 1, 'on', 1),
('20150109162309900000', 'image_bucket', 'image_bucket', 'file', 'file', '', 0, 'on', 1),
('2015010916230710000', 'image_bucket', 'image_bucket', 'file_path', 'file', '', 1, 'on', 1),
('20150109162300800000', 'sub_menus', 'sub_menus', 'c_options[shift]', 'function', 'text', 0, 'on', 1),
('201503301739532884551929400', 'component', 'components', 'max-width', 'css', '', 3, 'NUL', 0),
('201504040209558199551803318', 'sub_menus', 'sub_menus', 'c_options[url]', 'function', 'text', 0, 'on', 1);";
			
			if($create == true) {
					$nubquery->addCustom("DROP TABLE IF EXISTS `component_builder`",true)->write();
					$create	=	"CREATE TABLE IF NOT EXISTS `component_builder` (
  `ID` int(20) NOT NULL auto_increment,
  `unique_id` varchar(100) collate utf8_unicode_ci NOT NULL,
  `component_name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `assoc_table` text collate utf8_unicode_ci NOT NULL,
  `component_value` text collate utf8_unicode_ci NOT NULL,
  `variable_type` varchar(15) collate utf8_unicode_ci NOT NULL,
  `map_input` varchar(20) collate utf8_unicode_ci default NULL,
  `page_order` int(3) NOT NULL,
  `page_live` varchar(3) collate utf8_unicode_ci NOT NULL,
  `core_setting` int(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  KEY `unique_id` (`unique_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";

					$nubquery->addCustom($create,true)->write();
				}
			
			$sql	=	(isset($sql))? $sql:$insert['cols'].$insert['vals'];
			
			// Write new settings
			$nubquery->addCustom($sql,true)->write();
		}
?>