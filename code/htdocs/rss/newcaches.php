<?php

$opt['rootpath'] = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once('../lib2/web.inc.php');

$translator = new Language_Translator();
$generator = new rss_FeedGenerator(new rss_CachesFeedData($translator));

$generator->outputFeed();

?>
