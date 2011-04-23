<?php
	// Unicode Reminder メモ

	$mailfrom = 'watch@opencaching.se';
	$mailsubject = '[opencaching] Your watchlist from ' . date('Y-m-d');

	$debug = false;
	$debug_mailto = 'abc@xyz.de';
	
	$nologs = 'No new logs';
	
	$logowner_text = '{date} {user} has written a {logtype} log for your cache "{cachename}".' . "\n" . 'http://www.opencaching.se/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";
	$logwatch_text = '{date} {user} has written a {logtype} log for the cache "{cachename}".' . "\n" . 'http://www.opencaching.se/viewcache.php?cacheid={cacheid}' . "\n\n" . '{text}' . "\n\n\n\n";

	$watchpid = '/var/www/www.opencaching.de/html/cache/watch.pid';
?>
