<?php

$opt['rootpath'] = '../';
require_once($opt['rootpath'] . 'lib2/web.inc.php');

global $opt;

$translator = new Language_Translator();
$generator = new Rss_FeedGenerator(new Rss_CachesFeedData($translator, $opt['template']['locale']), $translator);

$generator->outputFeed();

?>
