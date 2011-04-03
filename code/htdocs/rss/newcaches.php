<?php

$opt['rootpath'] = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once('../lib2/web.inc.php');

global $opt;

$translator = new Language_Translator();
$generator = new Rss_FeedGenerator(new Rss_CachesFeedData($translator, $opt['template']['locale']));

$generator->outputFeed();

?>
