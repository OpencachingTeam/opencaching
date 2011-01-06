#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$opt['rootpath'] = '../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require($opt['rootpath'] . 'lib2/cli.inc.php');
	require($opt['rootpath'] . 'util2/demodb/settings.inc.php');
	require($opt['rootpath'] . 'config2/sqlroot.inc.php');
	$db['debug'] = false;
	
	$opt['db']['placeholder']['export'] = $export['dbname'];

	if (sql_connect_root() == false)
		$cli->fatal("Could not connect to DB as root!");
/*
	$sDBName = sql_value("SHOW DATABASES LIKE '&1'", '', $export['dbname']);
	if ($sDBName != '')
		$cli->fatal("Export DB already exist!");

	sql("CREATE DATABASE `&1` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci", $export['dbname']);
	sql("USE `&1`", $opt['db']['placeholder']['db']);
	$rsTables = sql("SHOW TABLES");
	while ($rTable = sql_fetch_array($rsTables))
	{
		$sTable = $rTable[0];
		$cli->out($sTable);

		sql("USE `&1`", $opt['db']['placeholder']['db']);
		$rsCreateSql = sql("SHOW CREATE TABLE `&1`", $sTable);
		$rCreateSql = sql_fetch_array($rsCreateSql);
		sql_free_result($rsCreateSql);
		
		$sCreateSql = $rCreateSql[1];
		
		sql("USE `&1`", $export['dbname']);
		sql($sCreateSql);
	}
	sql_free_result($rsTables);
*/

  CleanExportDB();

  $cli->out('Copying static data tables');
	sql("INSERT INTO &export.`cache_attrib` (`id`, `name`, `trans_id`, `category`, `default`, `icon_large`, `icon_no`, `icon_undef`) SELECT `id`, `name`, `trans_id`, `category`, `default`, `icon_large`, `icon_no`, `icon_undef` FROM &db.`cache_attrib`");
	sql("INSERT INTO &export.`cache_report_reasons` (`id`, `name`, `trans_id`) SELECT `id`, `name`, `trans_id` FROM &db.`cache_report_reasons`");
	sql("INSERT INTO &export.`cache_report_status` (`id`, `name`, `trans_id`) SELECT `id`, `name`, `trans_id` FROM &db.`cache_report_status`");
	sql("INSERT INTO &export.`cache_size` (`id`, `name`, `trans_id`, `de`, `en`) SELECT `id`, `name`, `trans_id`, `de`, `en` FROM &db.`cache_size`");
	sql("INSERT INTO &export.`cache_status` (`id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log`) SELECT `id`, `name`, `trans_id`, `de`, `en`, `allow_user_view`, `allow_owner_edit_status`, `allow_user_log` FROM &db.`cache_status`");
	sql("INSERT INTO &export.`cache_type` (`id`, `name`, `trans_id`, `short`, `de`, `en`, `icon_large`) SELECT `id`, `name`, `trans_id`, `short`, `de`, `en`, `icon_large` FROM &db.`cache_type`");
	sql("INSERT INTO &export.`countries` (`short`, `name`, `trans_id`, `de`, `en`, `list_default_de`, `sort_de`, `list_default_en`, `sort_en`) SELECT `short`, `name`, `trans_id`, `de`, `en`, `list_default_de`, `sort_de`, `list_default_en`, `sort_en` FROM &db.`countries`");
	sql("INSERT INTO &export.`countries_list_default` (`lang`, `show`) SELECT `lang`, `show` FROM &db.`countries_list_default`");
	sql("INSERT INTO &export.`languages` (`short`, `name`, `trans_id`, `de`, `en`, `list_default_de`, `list_default_en`) SELECT `short`, `name`, `trans_id`, `de`, `en`, `list_default_de`, `list_default_en` FROM &db.`languages`");
	sql("INSERT INTO &export.`languages_list_default` (`lang`, `show`) SELECT `lang`, `show` FROM &db.`languages_list_default`");
	sql("INSERT INTO &export.`log_types` (`id`, `name`, `trans_id`, `permission`, `cache_status`, `de`, `en`, `icon_small`) SELECT `id`, `name`, `trans_id`, `permission`, `cache_status`, `de`, `en`, `icon_small` FROM &db.`log_types`");
	sql("INSERT INTO &export.`log_types_text` (`id`, `log_types_id`, `lang`, `text_combo`, `text_listing`) SELECT `id`, `log_types_id`, `lang`, `text_combo`, `text_listing` FROM &db.`log_types_text`");
	sql("INSERT INTO &export.`logentries_types` (`id`, `module`, `eventname`) SELECT `id`, `module`, `eventname` FROM &db.`logentries_types`");
	sql("INSERT INTO &export.`news_topics` (`id`, `name`, `trans_id`) SELECT `id`, `name`, `trans_id` FROM &db.`news_topics`");
	sql("INSERT INTO &export.`nodes` (`id`, `name`, `url`, `waypoint_prefix`) SELECT `id`, `name`, `url`, `waypoint_prefix` FROM &db.`nodes`");
	sql("INSERT INTO &export.`object_types` (`id`, `name`) SELECT `id`, `name` FROM &db.`object_types`");
	sql("INSERT INTO &export.`profile_options` (`id`, `name`, `trans_id`, `internal_use`, `default_value`, `check_regex`, `option_order`, `option_input`) SELECT `id`, `name`, `trans_id`, `internal_use`, `default_value`, `check_regex`, `option_order`, `option_input` FROM &db.`profile_options`");
	sql("INSERT INTO &export.`search_ignore` (`word`) SELECT `word` FROM &db.`search_ignore`");
	sql("INSERT INTO &export.`statpics` (`id`, `tplpath`, `previewpath`, `description`, `maxtextwidth`) SELECT `id`, `tplpath`, `previewpath`, `description`, `maxtextwidth` FROM &db.`statpics`");
	sql("INSERT INTO &export.`sys_menu` (`id`, `id_string`, `title`, `title_trans_id`, `menustring`, `menustring_trans_id`, `access`, `href`, `visible`, `parent`, `position`, `color`, `sitemap`) SELECT `id`, `id_string`, `title`, `title_trans_id`, `menustring`, `menustring_trans_id`, `access`, `href`, `visible`, `parent`, `position`, `color`, `sitemap` FROM &db.`sys_menu`");
	sql("INSERT INTO &export.`sys_trans` (`id`, `text`, `last_modified`) SELECT `id`, `text`, `last_modified` FROM &db.`sys_trans`");
	sql("INSERT INTO &export.`sys_trans_ref` (`id`, `trans_id`, `style`, `resource_name`, `line`) SELECT `id`, `trans_id`, `style`, `resource_name`, `line` FROM &db.`sys_trans_ref`");
	sql("INSERT INTO &export.`sys_trans_text` (`trans_id`, `lang`, `text`, `last_modified`) SELECT `trans_id`, `lang`, `text`, `last_modified` FROM &db.`sys_trans_text`");
	sql("INSERT INTO &export.`watches_waitingtypes` (`id`, `watchtype`) SELECT `id`, `watchtype` FROM &db.`watches_waitingtypes`");

  $cli->out('Copying opengeodb');
	sql("INSERT INTO &export.`geodb_areas` (`loc_id`, `area_id`, `polygon_id`, `pol_seq_no`, `exclude_area`, `area_type`, `area_subtype`, `coord_type`, `coord_subtype`, `resolution`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `area_id`, `polygon_id`, `pol_seq_no`, `exclude_area`, `area_type`, `area_subtype`, `coord_type`, `coord_subtype`, `resolution`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_areas`");
	sql("INSERT INTO &export.`geodb_changelog` (`id`, `datum`, `beschreibung`, `autor`, `version`) SELECT `id`, `datum`, `beschreibung`, `autor`, `version` FROM &db.`geodb_changelog`");
	sql("INSERT INTO &export.`geodb_coordinates` (`loc_id`, `lon`, `lat`, `coord_type`, `coord_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `lon`, `lat`, `coord_type`, `coord_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_coordinates`");
	sql("INSERT INTO &export.`geodb_floatdata` (`loc_id`, `float_val`, `float_type`, `float_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `float_val`, `float_type`, `float_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_floatdata`");
	sql("INSERT INTO &export.`geodb_hierarchies` (`loc_id`, `level`, `id_lvl1`, `id_lvl2`, `id_lvl3`, `id_lvl4`, `id_lvl5`, `id_lvl6`, `id_lvl7`, `id_lvl8`, `id_lvl9`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `level`, `id_lvl1`, `id_lvl2`, `id_lvl3`, `id_lvl4`, `id_lvl5`, `id_lvl6`, `id_lvl7`, `id_lvl8`, `id_lvl9`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_hierarchies`");
	sql("INSERT INTO &export.`geodb_intdata` (`loc_id`, `int_val`, `int_type`, `int_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `int_val`, `int_type`, `int_subtype`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_intdata`");
	sql("INSERT INTO &export.`geodb_locations` (`loc_id`, `loc_type`) SELECT `loc_id`, `loc_type` FROM &db.`geodb_locations`");
	sql("INSERT INTO &export.`geodb_polygons` (`polygon_id`, `seq_no`, `lon`, `lat`) SELECT `polygon_id`, `seq_no`, `lon`, `lat` FROM &db.`geodb_polygons`");
	sql("INSERT INTO &export.`geodb_search` (`id`, `loc_id`, `sort`, `simple`, `simplehash`) SELECT `id`, `loc_id`, `sort`, `simple`, `simplehash` FROM &db.`geodb_search`");
	sql("INSERT INTO &export.`geodb_textdata` (`loc_id`, `text_val`, `text_type`, `text_locale`, `is_native_lang`, `is_default_name`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until`) SELECT `loc_id`, `text_val`, `text_type`, `text_locale`, `is_native_lang`, `is_default_name`, `valid_since`, `date_type_since`, `valid_until`, `date_type_until` FROM &db.`geodb_textdata`");
	sql("INSERT INTO &export.`geodb_type_names` (`type_id`, `type_locale`, `name`) SELECT `type_id`, `type_locale`, `name` FROM &db.`geodb_type_names`");

  $cli->out('Copying geokrety');
	sql("INSERT INTO &export.`gk_item` (`id`, `name`, `description`, `userid`, `datecreated`, `distancetravelled`, `latitude`, `longitude`, `typeid`, `stateid`) SELECT `id`, `name`, `description`, `userid`, `datecreated`, `distancetravelled`, `latitude`, `longitude`, `typeid`, `stateid` FROM &db.`gk_item`");
	sql("INSERT INTO &export.`gk_item_type` (`id`, `name`) SELECT `id`, `name` FROM &db.`gk_item_type`");
	sql("INSERT INTO &export.`gk_item_waypoint` (`id`, `wp`) SELECT `id`, `wp` FROM &db.`gk_item_waypoint`");
	sql("INSERT INTO &export.`gk_move` (`id`, `itemid`, `latitude`, `longitude`, `datemoved`, `datelogged`, `userid`, `comment`, `logtypeid`) SELECT `id`, `itemid`, `latitude`, `longitude`, `datemoved`, `datelogged`, `userid`, `comment`, `logtypeid` FROM &db.`gk_move`");
	sql("INSERT INTO &export.`gk_move_type` (`id`, `name`) SELECT `id`, `name` FROM &db.`gk_move_type`");
	sql("INSERT INTO &export.`gk_move_waypoint` (`id`, `wp`) SELECT `id`, `wp` FROM &db.`gk_move_waypoint`");
	sql("INSERT INTO &export.`gk_user` (`id`, `name`) SELECT `id`, `name` FROM &db.`gk_user`");

  $cli->out('Copying GNS');
	sql("INSERT INTO &export.`gns_locations` (`rc`, `ufi`, `uni`, `lat`, `lon`, `dms_lat`, `dms_lon`, `utm`, `jog`, `fc`, `dsg`, `pc`, `cc1`, `adm1`, `adm2`, `dim`, `cc2`, `nt`, `lc`, `SHORT_FORM`, `GENERIC`, `SORT_NAME`, `FULL_NAME`, `FULL_NAME_ND`, `MOD_DATE`, `admtxt1`, `admtxt3`, `admtxt4`, `admtxt2`) SELECT `rc`, `ufi`, `uni`, `lat`, `lon`, `dms_lat`, `dms_lon`, `utm`, `jog`, `fc`, `dsg`, `pc`, `cc1`, `adm1`, `adm2`, `dim`, `cc2`, `nt`, `lc`, `SHORT_FORM`, `GENERIC`, `SORT_NAME`, `FULL_NAME`, `FULL_NAME_ND`, `MOD_DATE`, `admtxt1`, `admtxt3`, `admtxt4`, `admtxt2` FROM &db.`gns_locations`");
	sql("INSERT INTO &export.`gns_search` (`id`, `uni_id`, `sort`, `simple`, `simplehash`) SELECT `id`, `uni_id`, `sort`, `simple`, `simplehash` FROM &db.`gns_search`");

  $cli->out('Copying NUTS');
	sql("INSERT INTO &export.`nuts_codes` (`code`, `name`) SELECT `code`, `name` FROM &db.`nuts_codes`");
	sql("INSERT INTO &export.`nuts_layer` (`id`, `level`, `code`, `shape`) SELECT `id`, `level`, `code`, `shape` FROM &db.`nuts_layer`");

	$cli->out('Copying main tables');
	sql("INSERT INTO &export.`user` (`user_id`, `uuid`, `node`, `date_created`, `last_modified`, `username`, `pmr_flag`, `statpic_logo`, `statpic_text`) SELECT `user_id`, `uuid`, `node`, `date_created`, `last_modified`, `username`, `pmr_flag`, `statpic_logo`, `statpic_text` FROM &db.`user` WHERE `is_active_flag`=1 AND `activation_code`=''");
	sql("INSERT INTO &export.`caches` (`cache_id`, `uuid`, `node`, `date_created`, `last_modified`, `user_id`, `name`, `longitude`, `latitude`, `type`, `status`, `country`, `date_hidden`, `size`, `difficulty`, `terrain`, `search_time`, `way_length`, `wp_gc`, `wp_nc`, `wp_oc`, `desc_languages`, `default_desclang`) SELECT &db.`caches`.`cache_id`, &db.`caches`.`uuid`, &db.`caches`.`node`, &db.`caches`.`date_created`, &db.`caches`.`last_modified`, &db.`caches`.`user_id`, &db.`caches`.`name`, &db.`caches`.`longitude`, &db.`caches`.`latitude`, &db.`caches`.`type`, &db.`caches`.`status`, &db.`caches`.`country`, &db.`caches`.`date_hidden`, &db.`caches`.`size`, &db.`caches`.`difficulty`, &db.`caches`.`terrain`, &db.`caches`.`search_time`, &db.`caches`.`way_length`, &db.`caches`.`wp_gc`, &db.`caches`.`wp_nc`, &db.`caches`.`wp_oc`, &db.`caches`.`desc_languages`, &db.`caches`.`default_desclang` FROM &db.`caches` INNER JOIN &export.`user` ON &db.`caches`.`user_id`=&export.`user`.`user_id` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE `cache_status`.`allow_user_view`=1");
	sql("INSERT INTO &export.`cache_desc` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `cache_id`, `language`, `desc`, `desc_html`, `desc_htmledit`, `hint`, `short_desc`) SELECT &db.`cache_desc`.`id`, &db.`cache_desc`.`uuid`, &db.`cache_desc`.`node`, &db.`cache_desc`.`date_created`, &db.`cache_desc`.`last_modified`, &db.`cache_desc`.`cache_id`, &db.`cache_desc`.`language`, &db.`cache_desc`.`desc`, &db.`cache_desc`.`desc_html`, &db.`cache_desc`.`desc_htmledit`, &db.`cache_desc`.`hint`, &db.`cache_desc`.`short_desc` FROM &db.`cache_desc` INNER JOIN &export.`caches` ON &db.`cache_desc`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`cache_logs` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `owner_notified`, `picture`) SELECT &db.`cache_logs`.`id`, &db.`cache_logs`.`uuid`, &db.`cache_logs`.`node`, &db.`cache_logs`.`date_created`, &db.`cache_logs`.`last_modified`, &db.`cache_logs`.`cache_id`, &db.`cache_logs`.`user_id`, &db.`cache_logs`.`type`, &db.`cache_logs`.`date`, &db.`cache_logs`.`text`, &db.`cache_logs`.`text_html`, &db.`cache_logs`.`text_htmledit`, &db.`cache_logs`.`owner_notified`, &db.`cache_logs`.`picture` FROM &db.`cache_logs` INNER JOIN &export.`caches` ON &db.`cache_logs`.`cache_id`=&export.`caches`.`cache_id` INNER JOIN &export.`user` ON &db.`cache_logs`.`user_id`=&export.`user`.`user_id`");
	sql("INSERT INTO &export.`pictures` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `url`, `title`, `object_id`, `object_type`, `spoiler`, `local`, `unknown_format`, `display`) SELECT &db.`pictures`.`id`, &db.`pictures`.`uuid`, &db.`pictures`.`node`, &db.`pictures`.`date_created`, &db.`pictures`.`last_modified`, &db.`pictures`.`url`, &db.`pictures`.`title`, &db.`pictures`.`object_id`, &db.`pictures`.`object_type`, &db.`pictures`.`spoiler`, 0 AS `local`, &db.`pictures`.`unknown_format`, &db.`pictures`.`display` FROM &db.`pictures` INNER JOIN &export.`cache_logs` ON &db.`pictures`.`object_id`=&export.`cache_logs`.`id` AND &db.`pictures`.`object_type`=1");
	sql("INSERT INTO &export.`pictures` (`id`, `uuid`, `node`, `date_created`, `last_modified`, `url`, `title`, `object_id`, `object_type`, `spoiler`, `local`, `unknown_format`, `display`) SELECT &db.`pictures`.`id`, &db.`pictures`.`uuid`, &db.`pictures`.`node`, &db.`pictures`.`date_created`, &db.`pictures`.`last_modified`, &db.`pictures`.`url`, &db.`pictures`.`title`, &db.`pictures`.`object_id`, &db.`pictures`.`object_type`, &db.`pictures`.`spoiler`, 0 AS `local`, &db.`pictures`.`unknown_format`, &db.`pictures`.`display` FROM &db.`pictures` INNER JOIN &export.`caches` ON &db.`pictures`.`object_id`=&export.`caches`.`cache_id` AND &db.`pictures`.`object_type`=2");
	sql("INSERT INTO &export.`removed_objects` (`id`, `localID`, `uuid`, `type`, `removed_date`, `node`) SELECT `id`, `localID`, `uuid`, `type`, `removed_date`, `node` FROM &db.`removed_objects`");

	$cli->out('Copying other tables');
	sql("INSERT INTO &export.`caches_attributes` (`cache_id`, `attrib_id`) SELECT &db.`caches_attributes`.`cache_id`, &db.`caches_attributes`.`attrib_id` FROM &db.`caches_attributes` INNER JOIN &export.`caches` ON &db.`caches_attributes`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`cache_coordinates` (`id`, `date_created`, `cache_id`, `longitude`, `latitude`) SELECT &db.`cache_coordinates`.`id`, &db.`cache_coordinates`.`date_created`, &db.`cache_coordinates`.`cache_id`, &db.`cache_coordinates`.`longitude`, &db.`cache_coordinates`.`latitude` FROM &db.`cache_coordinates` INNER JOIN &export.`caches` ON &db.`cache_coordinates`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`cache_countries` (`id`, `date_created`, `cache_id`, `country`) SELECT &db.`cache_countries`.`id`, &db.`cache_countries`.`date_created`, &db.`cache_countries`.`cache_id`, &db.`cache_countries`.`country` FROM &db.`cache_countries` INNER JOIN &export.`caches` ON &db.`cache_countries`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`cache_location` (`cache_id`, `last_modified`, `adm1`, `adm2`, `adm3`, `adm4`, `code1`, `code2`, `code3`, `code4`) SELECT &db.`cache_location`.`cache_id`, &db.`cache_location`.`last_modified`, &db.`cache_location`.`adm1`, &db.`cache_location`.`adm2`, &db.`cache_location`.`adm3`, &db.`cache_location`.`adm4`, &db.`cache_location`.`code1`, &db.`cache_location`.`code2`, &db.`cache_location`.`code3`, &db.`cache_location`.`code4` FROM &db.`cache_location` INNER JOIN &export.`caches` ON &db.`cache_location`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`cache_rating` (`cache_id`, `user_id`) SELECT &db.`cache_rating`.`cache_id`, &db.`cache_rating`.`user_id` FROM &db.`cache_rating` INNER JOIN &export.`caches` ON &db.`cache_rating`.`cache_id`=&export.`caches`.`cache_id` INNER JOIN &export.`user` ON &db.`cache_rating`.`user_id`=&export.`user`.`user_id`");
	sql("INSERT INTO &export.`cache_visits` (`cache_id`, `user_id_ip`, `count`, `last_modified`) SELECT &db.`cache_visits`.`cache_id`, &db.`cache_visits`.`user_id_ip`, &db.`cache_visits`.`count`, &db.`cache_visits`.`last_modified` FROM &db.`cache_visits` INNER JOIN &export.`caches` ON &db.`cache_visits`.`cache_id`=&export.`caches`.`cache_id` WHERE &db.`cache_visits`.`user_id_ip`='0'");
	sql("INSERT INTO &export.`search_index` (`object_type`, `cache_id`, `hash`, `count`) SELECT &db.`search_index`.`object_type`, &db.`search_index`.`cache_id`, &db.`search_index`.`hash`, &db.`search_index`.`count` FROM &db.`search_index` INNER JOIN &export.`caches` ON &db.`search_index`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`search_index_times` (`object_type`, `object_id`, `last_refresh`) SELECT &db.`search_index_times`.`object_type`, &db.`search_index_times`.`object_id`, &db.`search_index_times`.`last_refresh` FROM &db.`search_index_times` INNER JOIN `cache_logs` ON `search_index_times`.`object_id`=`cache_logs`.`id` AND `search_index_times`.`object_type`=1");
	sql("INSERT INTO &export.`search_index_times` (`object_type`, `object_id`, `last_refresh`) SELECT &db.`search_index_times`.`object_type`, &db.`search_index_times`.`object_id`, &db.`search_index_times`.`last_refresh` FROM &db.`search_index_times` INNER JOIN `caches` ON `search_index_times`.`object_id`=`caches`.`cache_id` AND `search_index_times`.`object_type`=2");
	sql("INSERT INTO &export.`search_index_times` (`object_type`, `object_id`, `last_refresh`) SELECT &db.`search_index_times`.`object_type`, &db.`search_index_times`.`object_id`, &db.`search_index_times`.`last_refresh` FROM &db.`search_index_times` INNER JOIN `cache_desc` ON `search_index_times`.`object_id`=`cache_desc`.`id` AND `search_index_times`.`object_type`=3");
	sql("INSERT INTO &export.`search_index_times` (`object_type`, `object_id`, `last_refresh`) SELECT &db.`search_index_times`.`object_type`, &db.`search_index_times`.`object_id`, &db.`search_index_times`.`last_refresh` FROM &db.`search_index_times` INNER JOIN `pictures` ON `search_index_times`.`object_id`=`pictures`.`id` AND `search_index_times`.`object_type`=6");
	sql("INSERT INTO &export.`stat_cache_logs` (`cache_id`, `user_id`, `found`, `notfound`, `note`) SELECT &db.`stat_cache_logs`.`cache_id`, &db.`stat_cache_logs`.`user_id`, &db.`stat_cache_logs`.`found`, &db.`stat_cache_logs`.`notfound`, &db.`stat_cache_logs`.`note` FROM &db.`stat_cache_logs` INNER JOIN &export.`caches` ON &db.`stat_cache_logs`.`cache_id`=&export.`caches`.`cache_id` INNER JOIN &export.`user` ON &db.`stat_cache_logs`.`user_id`=&export.`user`.`user_id`");
	sql("INSERT INTO &export.`stat_caches` (`cache_id`, `found`, `notfound`, `note`, `will_attend`, `last_found`, `watch`, `ignore`, `toprating`, `picture`) SELECT &db.`stat_caches`.`cache_id`, &db.`stat_caches`.`found`, &db.`stat_caches`.`notfound`, &db.`stat_caches`.`note`, &db.`stat_caches`.`will_attend`, &db.`stat_caches`.`last_found`, &db.`stat_caches`.`watch`, &db.`stat_caches`.`ignore`, &db.`stat_caches`.`toprating`, &db.`stat_caches`.`picture` FROM &db.`stat_caches` INNER JOIN &export.`caches` ON &db.`stat_caches`.`cache_id`=&export.`caches`.`cache_id`");
	sql("INSERT INTO &export.`stat_user` (`user_id`, `found`, `notfound`, `note`, `hidden`) SELECT &db.`stat_user`.`user_id`, &db.`stat_user`.`found`, &db.`stat_user`.`notfound`, &db.`stat_user`.`note`, &db.`stat_user`.`hidden` FROM &db.`stat_user` INNER JOIN &export.`user` ON &db.`stat_user`.`user_id`=&export.`user`.`user_id`");

function CleanExportDB()
{
	global $export;

	sql("USE `&1`", $export['dbname']);
	$rsTables = sql("SHOW TABLES");
	while ($rTable = sql_fetch_array($rsTables))
	{
		$sTable = $rTable[0];	
		sql("TRUNCATE TABLE `&1`", $sTable);
	}
	sql_free_result($rsTables);
}

function printOutInsertSql()
{
	global $opt;

	sql("USE `&1`", $opt['db']['placeholder']['db']);
	$rsTables = sql("SHOW TABLES");
	while ($rTable = sql_fetch_array($rsTables))
	{
		$sTable = $rTable[0];

		$aCols = array();
		$rsColumns = sql("SHOW COLUMNS FROM `&1`", $sTable);
		while ($rColumn = sql_fetch_assoc($rsColumns))
			$aCols[] = '`' . $rColumn['Field'] . '`';
		sql_free_result($rsColumns);

		$sColString = implode($aCols, ', ');
		
		echo 'INSERT INTO &export.`' . $sTable . '` (' . $sColString . ') SELECT &db.' . $sColString . ' FROM `' . $sTable . "`\n";
	}
	sql_free_result($rsTables);
}
?>