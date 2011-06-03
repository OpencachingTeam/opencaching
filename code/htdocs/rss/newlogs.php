<?php

$opt['rootpath'] = '../';
require_once($opt['rootpath'] . 'lib2/web.inc.php');

$translator = new Language_Translator();
$items = new Rss_LogItems();
$title = $translator->translate('New logs with gpx');
$atomLink = 'newlogs.xml';

$generator = new Rss_FeedGenerator(new Rss_LogsFeedData($translator, $items, $title, $atomLink), $translator);

$generator->outputFeed();

?>
