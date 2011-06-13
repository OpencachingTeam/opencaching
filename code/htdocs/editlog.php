<?php
/***************************************************************************
																./editlog.php
															-------------------
		begin                : July 5 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 edit a log listing

	 used template(s): editlog

	 GET/POST Parameter: logid
	 
	 Note: when changing recommendation, the last_modified-date of log-record
	       has to be updated to trigger resync via xml-interface

 ****************************************************************************/

  //prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require($stylepath.'/smilies.inc.php');
  require_once($opt['rootpath'] . '../lib/htmlpurifier-4.2.0/library/HTMLPurifier.auto.php');

	//Preprocessing
	if ($error == false)
	{
		//logid
		$log_id = 0;
		if (isset($_REQUEST['logid']))
		{
			$log_id = $_REQUEST['logid'];
		}

		if ($usr === false)
		{
			$tplname = 'login';

			tpl_set_var('username', '');
			tpl_set_var('message_start', '');
			tpl_set_var('message_end', '');
			tpl_set_var('target', 'editlog.php?logid=' . urlencode($log_id));
			tpl_set_var('message', $login_required);
		}
		else
		{
			//does log with this logid exist?
			$log_rs = sql("SELECT `cache_logs`.`cache_id` AS `cache_id`, `cache_logs`.`node` AS `node`, `cache_logs`.`text` AS `text`, `cache_logs`.`date` AS `date`, `cache_logs`.`user_id` AS `user_id`, `cache_logs`.`type` AS `logtype`, `cache_logs`.`text_html` AS `text_html`, `cache_logs`.`text_htmledit` AS `text_htmledit`, `caches`.`name` AS `cachename`, `caches`.`type` AS `cachetype`, `caches`.`user_id` AS `cache_user_id`, `caches`.`logpw` as `logpw`, `caches`.`status` as `status` FROM `cache_logs` INNER JOIN `caches` ON (`caches`.`cache_id`=`cache_logs`.`cache_id`) WHERE `id`='&1'", $log_id);
			$log_record = sql_fetch_array($log_rs);
			sql_free_result($log_rs);

			if ($log_record !== false && $log_record['status'] != 6 && $log_record['status'] != 7)
			{
				require($stylepath . '/editlog.inc.php');
				require($stylepath.'/rating.inc.php');

				if ($log_record['node'] != $oc_nodeid)
				{
					tpl_errorMsg('editlog', $error_wrong_node);
					exit;
				}

				//is this log from this user?
				if ($log_record['user_id'] == $usr['userid'])
				{
					$tplname = 'editlog';

					//load settings
					$cache_name = $log_record['cachename'];
					$cache_type = $log_record['cachetype'];
					$cache_user_id = $log_record['cache_user_id'];
					$log_type = isset($_POST['logtype']) ? $_POST['logtype'] : $log_record['logtype'];

					$log_date_day = isset($_POST['logday']) ? $_POST['logday'] : date('d', strtotime($log_record['date']));
					$log_date_month = isset($_POST['logmonth']) ? $_POST['logmonth'] : date('m', strtotime($log_record['date']));
					$log_date_year = isset($_POST['logyear']) ? $_POST['logyear'] : date('Y', strtotime($log_record['date']));
					$top_cache = isset($_POST['rating']) ? $_POST['rating']+0 : 0;

					$log_pw = '';
					$use_log_pw = (($log_record['logpw'] == NULL) || ($log_record['logpw'] == '')) ? false : true;
					if (($use_log_pw) && $log_record['logtype']==1)
						$use_log_pw = false;

					if ($use_log_pw)
						$log_pw = $log_record['logpw'];

					// check if user has exceeded his top5% limit
					$is_top = sqlValue("SELECT COUNT(`cache_id`) FROM `cache_rating` WHERE `user_id`='" . sql_escape($usr['userid']) . "' AND `cache_id`='" . sql_escape($log_record['cache_id']) . "'", 0);
					$user_founds = sqlValue("SELECT IFNULL(`found`, 0) FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE `user`.`user_id`='" .  sql_escape($usr['userid']) . "'", 0);
					$user_tops = sqlValue("SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 0);

					if ($is_top == 0)
					{
						if (($user_founds * rating_percentage/100) < 1)
						{
							$top_cache = 0;
							$anzahl = (1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
							if ($anzahl > 1)
							{
								$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
							}
							else
							{
								$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
							}
						}
						elseif ($user_tops < floor($user_founds * rating_percentage/100))
						{
							$rating_msg = mb_ereg_replace('{chk_sel}', '', $rating_allowed.'<br />'.$rating_stat);
							$rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage/100), $rating_msg);
							$rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
						}
						else
						{
							$top_cache = 0;
							$anzahl = ($user_tops + 1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
							if ($anzahl > 1)
							{
								$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
							}
							else
							{
								$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
							}
							$rating_msg .= '<br />'.$rating_maxreached;
						}
					}
					else
					{
						$rating_msg = mb_ereg_replace('{chk_sel}', ' checked', $rating_allowed.'<br />'.$rating_stat);
						$rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage/100), $rating_msg);
						$rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
					}

					tpl_set_var('rating_message', mb_ereg_replace('{rating_msg}', $rating_msg, $rating_tpl));

					if (isset($_POST['descMode']))
					{
						$descMode = $_POST['descMode']+0;
						if (($descMode < 1) || ($descMode > 3)) $descMode = 3;
					}
					else
					{
						if ($log_record['text_html'] == 1)
							if ($log_record['text_htmledit'] == 1)
								$descMode = 3;
							else
								$descMode = 2;
						else
							$descMode = 1;
					}

					// fuer alte Versionen von OCProp
					if (isset($_POST['submit']) && !isset($_POST['version2']))
					{
						$descMode = 1;
						$_POST['submitform'] = $_POST['submit'];
					}

					if ($descMode != 1)
					{
						// Text from textarea
						$log_text = isset($_POST['logtext']) ? ($_POST['logtext']) : ($log_record['text']);

						// fuer alte Versionen von OCProp
						if (isset($_POST['submit']) && !isset($_POST['version2']))
						{
							$log_text = iconv("ISO-8859-1", "UTF-8", $log_text);
						}

						// check input
						$purifier = new HTMLPurifier();
						$log_text = $purifier->purify($log_text);
					}
					else
					{
						// escape text
						$log_text = isset($_POST['logtext']) ? htmlspecialchars($_POST['logtext'], ENT_COMPAT, 'UTF-8') : strip_tags($log_record['text']);

						// fuer alte Versionen von OCProp
						if (isset($_POST['submit']) && !isset($_POST['version2']))
						{
							$log_text = iconv("ISO-8859-1", "UTF-8", $log_text);
						}
					}

					//validate date
					$date_not_ok = true;
					if (is_numeric($log_date_day) && is_numeric($log_date_month) && is_numeric($log_date_year))
					{
						if (checkdate($log_date_month, $log_date_day, $log_date_year) == true)
						{
							$date_not_ok = false;
						}
						if($date_not_ok == false)
						{
							if(isset($_POST['submitform']))
							{
								if(mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year)>=mktime())
								{
									$date_not_ok = true;
								}
								else
								{
									$date_not_ok = false;
								}
							}
						}
					}

					if ($cache_type == 6)
					{
						switch($log_type)
						{
							case 1:
							case 2:
								$logtype_not_ok = true;
								break;
							default:
								$logtype_not_ok = false;
								break;
						}
					}
					else
					{
						switch($log_type)
						{
							case 7:
							case 8:
								$logtype_not_ok = true;
								break;
							default:
								$logtype_not_ok = false;
								break;
						}
					}

					// not a found log? then ignore the rating
					if ($log_type != 1 && $log_type != 7)
					{
						$top_cache = 0;
					}


					$pw_not_ok = false;
					if (($use_log_pw) && $log_type == 1)
					{
						if (isset($_POST['log_pw']))
						{
							if (mb_strtolower($log_pw) != mb_strtolower($_POST['log_pw']))
							{
								$pw_not_ok = true;
								$all_ok = false;
							}
						}
						else
						{
							$pw_not_ok = true;
							$all_ok = false;
						}
					}

					//store?
					if (isset($_POST['submitform']) && $date_not_ok == false && $logtype_not_ok == false && $pw_not_ok == false)
					{
						//store changed data
						sql("UPDATE `cache_logs` SET `type`='&1',
						                             `date`='&2',
						                             `text`='&3',
						                             `text_html`='&4',
						                             `text_htmledit`='&5'
						                       WHERE `id`='&6'",
						                             $log_type,
						                             date('Y-m-d', mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year)),
						                             (($descMode != 1) ? $log_text : nl2br($log_text)),
						                             (($descMode != 1) ? 1 : 0),
						                             (($descMode == 3) ? 1 : 0),
						                             $log_id);

						//update user-stat if type changed
						if ($log_record['logtype'] != $log_type)
						{
							//call eventhandler
							require_once($opt['rootpath'] . 'lib/eventhandler.inc.php');
							event_change_log_type($log_record['cache_id'], $usr['userid']+0);
						}

						// update top-list
						if ($top_cache == 1)
							sql("INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`) VALUES('&1', '&2')", $usr['userid'], $log_record['cache_id']);
						else
							sql("DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'", $usr['userid'], $log_record['cache_id']);

						// do not use slave server for the next time ...
						db_slave_exclude();

						//display cache page
						tpl_redirect('viewcache.php?cacheid=' . urlencode($log_record['cache_id']));
						exit;
					}

					//build logtypeoptions
					$logtypeoptions = '';
					$rsLogTypes = sql("SELECT `log_types`.`id`, IFNULL(`sys_trans_text`.`text`, `log_types`.`name`) AS `name`
											         FROM `caches` 
								         INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id` 
								         INNER JOIN `cache_logtype` ON `cache_type`.`id`=`cache_logtype`.`cache_type_id` 
								         INNER JOIN `log_types` ON `cache_logtype`.`log_type_id`=`log_types`.`id` 
									        LEFT JOIN `sys_trans` ON `log_types`.`trans_id`=`sys_trans`.`id` 
									        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "' 
											        WHERE `caches`.`cache_id`='" . ($log_record['cache_id']+0) . "'
											     ORDER BY `log_types`.`id` ASC");
					while ($rLogTypes = sql_fetch_assoc($rsLogTypes))
					{
						$sSelected = ($rLogTypes['id'] == $log_type) ? ' selected="selected"' : '';
						$logtypeoptions .= '<option value="' . $rLogTypes['id'] . '"' . $sSelected . '>' . htmlspecialchars($rLogTypes['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
					}
					sql_free_result($rsLogTypes);

					//set template vars
					tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logtypeoptions', $logtypeoptions);
					tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cachename', htmlspecialchars($cache_name, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', $log_record['cache_id']);
					tpl_set_var('reset', $reset);
					tpl_set_var('submit', $submit);
					tpl_set_var('logid', $log_id);
					tpl_set_var('date_message', ($date_not_ok == true) ? $date_message : '');

					if ($descMode != 1)
						tpl_set_var('logtext', htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'), true);
					else
						tpl_set_var('logtext', $log_text);

					// Text / normal HTML / HTML editor
					tpl_set_var('use_tinymce', (($descMode == 3) ? 1 : 0));

					if ($descMode == 1)
						tpl_set_var('descMode', 1);
					else if ($descMode == 2)
						tpl_set_var('descMode', 2);
					else
					{
						// TinyMCE
						$headers = tpl_get_var('htmlheaders') . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/log.js.php?logid=0"></script>' . "\n";
						tpl_set_var('htmlheaders', $headers);

						tpl_set_var('descMode', 3);
					}

					if ($use_log_pw == true && $log_pw != '')
					{
						if ($pw_not_ok == true && isset($_POST['submitform']))
						{
							tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
						}
						else
						{
							tpl_set_var('log_pw_field', $log_pw_field);
						}
					}
					else
					{
						tpl_set_var('log_pw_field', '');
					}

					// build smilies
					$smilies = '';
					if ($descMode != 3)
					{
						for($i=0; $i<count($smileyshow); $i++)
						{
							if($smileyshow[$i] == '1')
							{
								$tmp_smiley = $smiley_link;
								$tmp_smiley = mb_ereg_replace('{smiley_image}', $smileyimage[$i], $tmp_smiley);
								$smilies = $smilies.mb_ereg_replace('{smiley_text}', ' '.$smileytext[$i].' ', $tmp_smiley).'&nbsp;';
							}
						}
					}
					tpl_set_var('smilies', $smilies);
				}
				else
				{
					//TODO: show error
				}
			}
			else
			{
				//TODO: show error
			}
		}
	}

	//make the template and send it out
	tpl_BuildTemplate();
?>