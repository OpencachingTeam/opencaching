<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  action   = new
 *           = edit
 *           = delete
 *  redirect = target page (default is viewcache)
 *
 *  Only one of the ids has to be set
 *  uuid      = id of the podcast (to edit or delete)
 *  cacheuuid = id of the cache (only for new podcasts)
 *
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/podcast.class.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/cachelog.class.php');
	$tpl->name = 'podcast';
	$tpl->menuitem = MNU_CACHES_PODCAST;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	$action = isset($_REQUEST['action']) ? mb_strtolower($_REQUEST['action']) : '';
	$redirect = isset($_REQUEST['redirect']) ? $_REQUEST['redirect'] : '';
	$redirect = $tpl->checkTarget($redirect, '');
	$tpl->assign('action', $action);
	$tpl->assign('redirect', $redirect);

	if ($action == 'add')
	{
		$podcast = new podcast();

		if (isset($_REQUEST['cacheuuid']))
		{
			$cache = cache::fromUUID($_REQUEST['cacheuuid']);
			if ($cache === null)
				$tpl->error(ERROR_CACHE_NOT_EXISTS);

			if ($cache->allowEdit() == false)
				$tpl->error(ERROR_NO_ACCESS);

			$podcast->setCacheId($cache->getCacheId());

			$cache = null;
		}
		else
			$tpl->error(ERROR_INVALID_OPERATION);

		// uploaded file ok?
		if (isset($_REQUEST['ok']))
		{
			$bError = false;

			$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
			if ($title == '')
			{
				$tpl->assign('errortitle', true);
				$bError = true;
			}
			else
				$podcast->setTitle($title);

			if (!isset($_FILES['file']))
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_NO_FILE);
				$bError = true;
			}
			else if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE)
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_NO_FILE);
				$bError = true;
			}
			else if ($_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE || $_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE)
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_SIZE);
				$bError = true;
			}
			else if ($_FILES['file']['error'] != UPLOAD_ERR_OK)
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_UNKNOWN);
				$bError = true;
			}
			else if ($_FILES['file']['size'] > $opt['logic']['podcasts']['maxsize'])
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_SIZE);
				$bError = true;
			}
			else if ($podcast->allowedExtension($_FILES['file']['name']) == false)
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_TYPE);
				$bError = true;
			}
			
			if ($bError == false)
			{
				$podcast->setFilenames($_FILES['file']['name']);
				$podcast->setLocal(1);

				// try saving file and record
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $podcast->getFilename()))
				{
					$tpl->assign('errorfile', ERROR_UPLOAD_UNKNOWN);
					$bError = true;
				}
				else if ($podcast->save())
				{
					if ($redirect == '')
						$redirect = 'viewcache.php?cacheid=' . urlencode($podcast->getCacheId());
					$tpl->redirect($redirect);
				}
				else
				{
					$tpl->assign('errorfile', ERROR_UPLOAD_UNKNOWN);
					$bError = true;
				}
			}
		}
	}
	else if ($action == 'edit' || $action == 'delete')
	{
		$uuid = isset($_REQUEST['uuid']) ? $_REQUEST['uuid'] : 0;
		$podcast = podcast::fromUUID($uuid);

		if ($podcast === null)
			$tpl->error(ERROR_PODCAST_NOT_EXISTS);

		if ($redirect == '')
			$redirect = 'viewcache.php?cacheid=' . urlencode($podcast->getCacheId());

		if ($podcast->allowEdit() == false)
			$tpl->error(ERROR_NO_ACCESS);

		if ($action == 'edit')
		{
			if (isset($_REQUEST['ok']))
			{
				// overwrite values
				$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : $podcast->getTitle();
				if ($title == '')
					$tpl->assign('errortitle', true);
				else
				{
					$podcast->setTitle($title);

					$podcast->save();
					$tpl->redirect($redirect);
				}
			}
		}
		else if ($action == 'delete')
		{
			if ($podcast->delete() == false)
				$tpl->error(ERROR_NO_ACCESS);

			$tpl->redirect($redirect);
		}
		else
			$tpl->error(ERROR_INVALID_OPERATION);
	}
	else
		$tpl->error(ERROR_INVALID_OPERATION);

	// prepare output
	$tpl->assign('uuid', $podcast->getUUID());

	if ($action == 'add')
	{
		$tpl->assign('cacheuuid', $_REQUEST['cacheuuid']);
	}

	$rsCache = sql("SELECT `wp_oc`, `name` FROM `caches` WHERE `cache_id`='&1'", $podcast->getCacheId());
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	$tpl->assign('cachewp', $rCache['wp_oc']);
	$tpl->assign('cachename', $rCache['name']);

	$tpl->assign('title', $podcast->getTitle());

	$tpl->display();
?>