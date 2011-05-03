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
 *  uuid      = id of the picture (to edit or delete)
 *  cacheuuid = id of the cache (only for new pictures)
 *  loguuid   = id of the logentry (only for new pictures)
 *
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/picture.class.php');
	require_once('./lib2/logic/cache.class.php');
	require_once('./lib2/logic/cachelog.class.php');
	$tpl->name = 'picture';
	$tpl->menuitem = MNU_CACHES_PICTURE;

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
		$picture = new picture();

		if (isset($_REQUEST['cacheuuid']))
		{
			$cache = cache::fromUUID($_REQUEST['cacheuuid']);
			if ($cache === null)
				$tpl->error(ERROR_CACHE_NOT_EXISTS);

			if ($cache->allowEdit() == false)
				$tpl->error(ERROR_NO_ACCESS);

			$picture->setObjectId($cache->getCacheId());
			$picture->setObjectType(OBJECT_CACHE);

			$cache = null;
		}
		else if (isset($_REQUEST['loguuid']))
		{
			$cachelog = cachelog::fromUUID($_REQUEST['loguuid']);
			if ($cachelog === null)
				$tpl->error(ERROR_CACHELOG_NOT_EXISTS);

			if ($cachelog->allowView() == false)
				$tpl->error(ERROR_NO_ACCESS);
			else if ($cachelog->allowEdit() == false)
				$tpl->error(ERROR_NO_ACCESS);

			$picture->setObjectId($cachelog->getLogId());
			$picture->setObjectType(OBJECT_CACHELOG);

			$cachelog = null;
		}
		else
			$tpl->error(ERROR_INVALID_OPERATION);

		// uploaded file ok?
		if (isset($_REQUEST['ok']))
		{
			$bError = false;

			$picture->setSpoiler(isset($_REQUEST['spoiler']) && $_REQUEST['spoiler']=='1');
			$picture->setDisplay((isset($_REQUEST['notdisplay']) && $_REQUEST['notdisplay']=='1') == false);

			$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : '';
			if ($title == '')
			{
				$tpl->assign('errortitle', true);
				$bError = true;
			}
			else
				$picture->setTitle($title);

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
			else if ($picture->allowedExtension($_FILES['file']['name']) == false)
			{
				$tpl->assign('errorfile', ERROR_UPLOAD_ERR_TYPE);
				$bError = true;
			}
			
			if ($bError == false)
			{
				$picture->setFilenames($_FILES['file']['name']);
				$picture->setLocal(1);
			}
			
			if ($bError == false)
			{
				if ($_FILES['file']['size'] > $opt['logic']['pictures']['maxsize'])
				{
					if (!$picture->resize($_FILES['file']['tmp_name']))
					{
						$tpl->assign('errorfile', ERROR_UPLOAD_ERR_SIZE);
						$bError = true;
					}
				}
			}

			if ($bError == false)
			{
				// try saving file and record
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $picture->getFilename()))
				{
					$tpl->assign('errorfile', ERROR_UPLOAD_UNKNOWN);
					$bError = true;
				}
				else if ($picture->save())
				{
					if ($redirect == '')
						$redirect = 'viewcache.php?cacheid=' . urlencode($picture->getCacheId());
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
		$picture = picture::fromUUID($uuid);

		if ($picture === null)
			$tpl->error(ERROR_PICTURE_NOT_EXISTS);

		if ($redirect == '')
			$redirect = 'viewcache.php?cacheid=' . urlencode($picture->getCacheId());

		if ($picture->allowEdit() == false)
			$tpl->error(ERROR_NO_ACCESS);

		if ($action == 'edit')
		{
			if (isset($_REQUEST['ok']))
			{
				// overwrite values
				$picture->setSpoiler(isset($_REQUEST['spoiler']) && $_REQUEST['spoiler']=='1');
				$picture->setDisplay((isset($_REQUEST['notdisplay']) && $_REQUEST['notdisplay']=='1') == false);

				$title = isset($_REQUEST['title']) ? $_REQUEST['title'] : $picture->getTitle();
				if ($title == '')
					$tpl->assign('errortitle', true);
				else
				{
					$picture->setTitle($title);

					$picture->save();
					$tpl->redirect($redirect);
				}
			}
		}
		else if ($action == 'delete')
		{
			if ($picture->delete() == false)
				$tpl->error(ERROR_NO_ACCESS);

			$tpl->redirect($redirect);
		}
		else
			$tpl->error(ERROR_INVALID_OPERATION);
	}
	else
		$tpl->error(ERROR_INVALID_OPERATION);

	// prepare output
	$tpl->assign('uuid', $picture->getUUID());
	$tpl->assign('objecttype', $picture->getObjectType());

	if ($action == 'add')
	{
		if ($picture->getObjectType() == OBJECT_CACHE)
			$tpl->assign('cacheuuid', $_REQUEST['cacheuuid']);
		else if ($picture->getObjectType() == OBJECT_CACHELOG)
			$tpl->assign('loguuid', $_REQUEST['loguuid']);
	}

	$rsCache = sql("SELECT `wp_oc`, `name` FROM `caches` WHERE `cache_id`='&1'", $picture->getCacheId());
	$rCache = sql_fetch_assoc($rsCache);
	sql_free_result($rsCache);

	$tpl->assign('cachewp', $rCache['wp_oc']);
	$tpl->assign('cachename', $rCache['name']);

	$tpl->assign('title', $picture->getTitle());
	$tpl->assign('spoilerchecked', $picture->getSpoiler());
	$tpl->assign('displaychecked', $picture->getDisplay());

	$tpl->display();
?>