<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/cachelog.class.php');
	require_once('./lib2/logic/smileys.class.php');
	$tpl->name = 'log2';
	$tpl->menuitem = MNU_CACHES_LOG;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	/*  create cachelog-object
	    cacheid => new log
	    logid   => edit log
	 */
	$nCacheId = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
	$nLogId = isset($_REQUEST['logid']) ? ($_REQUEST['logid']+0) : 0;

	if ($nLogId != 0)
	{
		$cachelog = new cachelog($nLogId);
		if ($cachelog->exist() == false)
			$tpl->error(ERROR_CACHELOG_NOT_EXISTS);
		if ($cachelog->allowEdit() == false)
			$tpl->error(ERROR_INVALID_OPERATION);

		$nCacheId = $cachelog->getCacheId();
	}
	else
	{
		$cachelog = cachelog::createNew($nCacheId, $login->userid);
		if ($cachelog === false)
			$tpl->error(ERROR_INVALID_OPERATION);
		$cachelog->setNode($opt['logic']['node']['id']);
	}

	// check cache exists
	$cache = new cache($nCacheId);
	if ($cache->exist() == false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);
	if ($cache->allowLog() == false)
		$tpl->error(ERROR_INVALID_OPERATION);

	/* read submitted data
	 */
	$nDescMode = isset($_POST['descMode']) ? $_POST['descMode']+0 : 0;
	if (($nDescMode < 1) || ($nDescMode > 3)) $nDescMode = 0;
	if ($nDescMode == 0)
	{
		if (sql_value("SELECT `no_htmledit_flag` FROM `user` WHERE `user_id`='&1'", 1, $login->userid) == 1)
			$nDescMode = 1;
		else
			$nDescMode = 3;
	}
	$cachelog->setTextHtml($nDescMode == 2 || $nDescMode == 3);
	$cachelog->setTextHtmlEdit($nDescMode == 3);

	/* read logtext and filter invalid HTML tags or prepare memo input
	 */
	$sLogText = isset($_POST['logtext']) ? ($_POST['logtext']) : '';
	if ($nDescMode != 1)
	{
		// check input
		$purifier = new HTMLPurifier();
		$sLogText = $purifier->purify($sLogText);
	}
	else
	{
		// escape text
		$sLogText = htmlspecialchars($sLogText, ENT_COMPAT, 'UTF-8');
	}
	$cachelog->setText($sLogText);

	/* read and validate date
	 */
	$nLogDateDay = isset($_POST['logday']) ? ($_POST['logday']+0) : date('d');
	$nLogDateMonth = isset($_POST['logmonth']) ? ($_POST['logmonth']+0) : date('m');
	$nLogDateYear = isset($_POST['logyear']) ? ($_POST['logyear']+0) : date('Y');

	$bDateFormatInvalid = false;
	if (is_numeric($nLogDateMonth) && is_numeric($nLogDateDay) && is_numeric($nLogDateYear))
	{
		$bDateFormatInvalid = (checkdate($nLogDateMonth, $nLogDateDay, $nLogDateYear) == false);
		if ($bDateFormatInvalid == false)
		{
			$nLogDate = mktime(0, 0, 0, $nLogDateMonth, $nLogDateDay, $nLogDateYear);
			$bDateFormatInvalid = ($nLogDate > time());
			
			if ($bDateFormatInvalid == true)
			{
				$cachelog->setDate($nLogDate);
			}
		}
	}

	// for a new logentry, we can set the logtype
	if ($cachelog->exist() == false)
	{
		$nLogType = isset($_POST['logtype']) ? ($_POST['logtype']+0) : 0;
		if ($nLogType != 0)
		{
			// set and validate log type
			if ($cachelog->setType($nLogType) == false)
				$nLogType = 0;
		}
	}

	/* validate log pw
	 */
	if ($nLogType != 0)
	{
		$sLogPW = isset($_POST['log_pw']) ? ($_POST['log_pw']) : '';
		$bLogPWValid = $cache->validateLogPW($nLogType, $sLogPW);
	}
	else
		$bLogPWValid = true;




	/* handle recommendations
	 */
	$bAddRecommendation = isset($_POST['addRecommendation']) ? ($_POST['addRecommendation']+0 != 0) : false;
	$bRevokeRecommendation = isset($_POST['revokeRecommendation']) ? ($_POST['revokeRecommendation']+0 != 0) : false;

	// TODO: implement in recommendation class to handle this 
	// recommendation allowed?
	if ($nLogType != 0)
	{
		if ($bAddRecommendation == true)
		{
			if (sql_value("SELECT `allow_rating` FROM `log_types` WHERE `id`='&1'", 0, $nLogType) == 0)
				$bAddRecommendation = false;
		}
	}

	// TODO: implement in recommendation class to handle this 
	// check if user has exceeded his recommendation limit
	$nUserFounds = sql_value("SELECT IFNULL(`stat_user`.`found`, 0) FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE `user`.`user_id`='&1'", 0, $login->userid);
	$nUserRecommendationPossible = floor($nUserFounds * $opt['logic']['rating']['percentageOfFounds']/100);
	$nUserRecommendationUsed = sql_value("SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='&1'", 0, $login->userid);

	$nUserRecommendationRequiredFinds = 0;
	if ($nUserRecommendationUsed >= $nUserRecommendationPossible)
	{
		$nUserRecommendationRequiredFinds = $opt['logic']['rating']['percentageOfFounds'] - ($nUserFounds % $opt['logic']['rating']['percentageOfFounds']);
		$bAddRecommendation = false;
	}

	// data submitted?
	if (isset($_POST['submit']) || isset($_POST['submitform']))
	{
		// prevent logs from old ocprop-client
		if (!isset($_POST['version3']))
		{
			die('Your client may be outdated!');
		}

		if (($bLogPWValid == true) && ($nLogType != 0) && ($bDateFormatInvalid == false))
		{
			// save log entry
			$cachelog->setType($nLogType);
	
			if ($cachelog->save() == false)
				$tpl->error(ERROR_UNKNOWN);

			// save or delete cache recommendation
			if ($bAddRecommendation == true)
				$cache->addRecommendation($login->userid);
			if ($bRevokeRecommendation == true)
				$cache->removeRecommendation($login->userid);

			$tpl->redirect('viewcache.php?wp=' . urlencode($cache->getWPOC()));
		}
	}

	/* prepare output
	 */

	if ($nDescMode == 3)
	{
		$tpl->add_header_javascript('resource2/tinymce/tiny_mce_gzip.js');
		$tpl->add_header_javascript('resource2/tinymce/config/log.js.php?logid=0&lang=' . strtolower($opt['template']['locale']));
	}
	else
	{
		$tpl->assign('smileys', smileys::getSmileysArray());
	}

	// build logtype list
	$rs = sql("SELECT `log_types`.`id`, IFNULL(`sys_trans_text`.`text`, `log_types`.`name`) AS `name`
	             FROM `caches` 
	       INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id` 
	       INNER JOIN `cache_logtype` ON `cache_type`.`id`=`cache_logtype`.`cache_type_id` 
	       INNER JOIN `log_types` ON `cache_logtype`.`log_type_id`=`log_types`.`id` 
	        LEFT JOIN `sys_trans` ON `log_types`.`trans_id`=`sys_trans`.`id` 
	        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1' 
	            WHERE `caches`.`cache_id`='&2'", $opt['template']['locale'], $nCacheId);
	$tpl->assign_rs('logtypes', $rs);
	sql_free_result($rs);

	$tpl->assign('logType', $nLogType);
	$tpl->assign('descMode', $nDescMode);

	if ($nDescMode != 1)
		$tpl->assign('logText', htmlspecialchars($sLogText, ENT_COMPAT, 'UTF-8'), true);
	else
		$tpl->assign('logText', $sLogText);

	$tpl->assign('dateFormatInvalid', $bDateFormatInvalid);
	$tpl->assign('requireLogPW', $cache->requireLogPW());
	$tpl->assign('logPWValid', $bLogPWValid);
	$tpl->assign('logDateDay', $nLogDateDay);
	$tpl->assign('logDateMonth', $nLogDateMonth);
	$tpl->assign('logDateYear', $nLogDateYear);

	$tpl->assign('userRecommended', $cache->isRecommendedByUser($login->userid));
	$tpl->assign('userRecommendationPossible', $nUserRecommendationPossible);
	$tpl->assign('userRecommendationUsed', $nUserRecommendationUsed);
	$tpl->assign('userRecommendationRequiredFinds', $nUserRecommendationRequiredFinds);

	// TODO: $cache->getTemplateInfo()
	$rCache['cacheid'] = $nCacheId;
	$rCache['cachename'] = $cache->getName();
	$tpl->assign('cache', $rCache);

	$tpl->display();
?>