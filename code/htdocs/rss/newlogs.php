<?php

$opt['rootpath'] = $_SERVER['DOCUMENT_ROOT'] . '/';
require_once('../lib2/web.inc.php');

$translator = new Language_Translator();
$items = new Rss_LogItems();
$title = $translator->translate('New logs with gpx');
$atomLink = 'newlogs.xml';

$generator = new Rss_FeedGenerator(new Rss_LogsFeedData($translator, $items, $title, $atomLink));

$generator->outputFeed();

?>
