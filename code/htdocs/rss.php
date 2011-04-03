<?php

	require('./lib2/web.inc.php');

	$tpl->name = 'rss';
	$tpl->menuitem = MNU_START_RSS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 3600;

	$tpl->display();
?>