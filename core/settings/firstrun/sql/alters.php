<?php
$alter[]	=	"ALTER TABLE `api`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `unique_id` (`unique_id`);";

$alter[]	=	"ALTER TABLE `component_builder`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `component_name` (`component_name`,`assoc_table`,`component_value`,`page_live`);";

$alter[]	=	"ALTER TABLE `component_locales`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `comp_id` (`comp_id`,`locale_abbr`,`page_live`);";

$alter[]	=	"ALTER TABLE `components`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `ref_page` (`ref_page`,`ref_anchor`,`category_id`,`component_type`,`page_live`),
  ADD KEY `title` (`title`);";

$alter[]	=	"ALTER TABLE `dropdown_menus`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `assoc_column` (`assoc_column`),
  ADD KEY `unique_id` (`unique_id`);";

$alter[]	=	"ALTER TABLE `emailer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `email_id` (`email_id`),
  ADD KEY `unique_id` (`unique_id`,`email_id`);";

$alter[]	=	"ALTER TABLE `file_activity`
  ADD PRIMARY KEY (`ID`);";

$alter[]	=	"ALTER TABLE `file_types`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`);";

$alter[]	=	"ALTER TABLE `form_builder`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `unique_id` (`unique_id`),
  ADD KEY `column_type` (`column_type`,`column_name`,`page_live`);";

$alter[]	=	"ALTER TABLE `main_menus`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `parent_id` (`parent_id`,`full_path`(255),`group_id`,`link`,`page_live`);";

$alter[]	=	"ALTER TABLE `media`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `unique_id` (`unique_id`);";

$alter[]	=	"ALTER TABLE `members_connected`
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD UNIQUE KEY `username` (`username`);";

$alter[]	=	"ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`),
  ADD KEY `page_element` (`page_element`,`name`,`component`,`usergroup`,`page_live`);";

$alter[]	=	"ALTER TABLE `upload_directory`
  ADD UNIQUE KEY `file_path` (`file_path`),
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);";

$alter[]	=	"ALTER TABLE `users`
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ID_UNIQUE` (`ID`),
  ADD KEY `password` (`password`(255)),
  ADD KEY `user_status` (`user_status`);";

$alter[]	=	"ALTER TABLE `api`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `component_builder`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `component_locales`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `components`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `dropdown_menus`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `emailer`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `file_activity`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `file_types`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `form_builder`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `main_menus`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `media`
  MODIFY `ID` bigint(50) UNSIGNED NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `members_connected`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `system_settings`
  MODIFY `ID` int(20) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `upload_directory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";

$alter[]	=	"ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;";