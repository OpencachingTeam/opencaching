<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/
 
	require('./lib2/web.inc.php');
	require('./lib2/logic/class.inputfilter.php');

	$tpl->name = 'htmlprev';
	$tpl->menuitem = MNU_CACHES_HIDE_PREVIEW;

	$the_text = isset($_REQUEST['thetext']) ? $_REQUEST['thetext'] : '';
	$the_html = isset($_REQUEST['thehtml']) ? $_REQUEST['thehtml'] : '';
	$step = isset($_REQUEST['step']) ? $_REQUEST['step']+0 : 1;

	if (isset($_REQUEST['toStep2']))
	{
		$tpl->assign('step', 2);

		if ($step == 1)
			$the_html = nl2br(htmlspecialchars($the_text, ENT_COMPAT, 'UTF-8'));
	}
	else if (isset($_REQUEST['toStep3']))
		$tpl->assign('step', 3);
	else
		$tpl->assign('step', 1);

	$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
	$the_html = $myFilter->process($the_html);

	$tpl->assign('thetext', $the_text);
	$tpl->assign('thehtml', $the_html);

	$tpl->display();
?>