<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');
	$tpl->name = 'tops';
	$tpl->menuitem = MNU_CACHES_TOPS;

	$tpl->caching = true;
	$tpl->cache_lifetime = 86400;

	if (!$tpl->is_cached())
	{
		sql_temp_table_slave('topLocationCaches');
		sql_temp_table_slave('topRatings');
		sql_temp_table_slave('topResult');
		
		sql_slave("CREATE TEMPORARY TABLE &topLocationCaches (`cache_id` INT(11) PRIMARY KEY) ENGINE=MEMORY");
		sql_slave("CREATE TEMPORARY TABLE &topRatings (`cache_id` INT(11) PRIMARY KEY, `ratings` INT(11)) ENGINE=MEMORY");
		sql_slave("CREATE TEMPORARY TABLE &topResult (`idx` INT(11), `cache_id` INT(11) PRIMARY KEY, `ratings` INT(11), `founds` INT(11)) ENGINE=MEMORY");

		$tops = array();
		$adm1Group = array();

		$rsAdm1 = sql_slave('SELECT SQL_BUFFER_RESULT SQL_SMALL_RESULT DISTINCT `adm1`, `code1` FROM `cache_location` WHERE NOT ISNULL(`adm1`) ORDER BY `adm1` ASC');
		while ($rAdm1 = sql_fetch_assoc($rsAdm1))
		{
			$adm1Group['name'] = $rAdm1['adm1'];
			$adm3Group = array();

			$rsAdm3 = sql_slave("SELECT SQL_BUFFER_RESULT SQL_SMALL_RESULT DISTINCT `adm3` FROM `cache_location` WHERE `code1`='&1' ORDER BY `adm3` ASC", $rAdm1['code1']);
			while ($rAdm3 = sql_fetch_assoc($rsAdm3))
			{
				$adm3Group['name'] = $rAdm3['adm3'];

				sql_slave("TRUNCATE TABLE &topLocationCaches");
				sql_slave("TRUNCATE TABLE &topRatings");
				sql_slave("TRUNCATE TABLE &topResult");

				// Alle Caches für diese Gruppe finden
				if ($adm3Group['name'] == null)
					sql_slave("INSERT INTO &topLocationCaches (`cache_id`) SELECT `caches`.`cache_id` FROM `cache_location` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_location`.`cache_id` LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` WHERE IFNULL(`stat_caches`.`toprating`,0)>0 AND `cache_location`.`adm1`='&1' AND ISNULL(`cache_location`.`adm3`) AND `caches`.`status`=1", $adm1Group['name']);
				else
					sql_slave("INSERT INTO &topLocationCaches (`cache_id`) SELECT `caches`.`cache_id` FROM `cache_location` INNER JOIN `caches` ON `caches`.`cache_id`=`cache_location`.`cache_id` LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id` WHERE IFNULL(`stat_caches`.`toprating`,0)>0 AND `cache_location`.`adm1`='&1' AND `cache_location`.`adm3`='&2' AND `caches`.`status`=1", $adm1Group['name'], $adm3Group['name']);

				sql_slave("INSERT INTO &topRatings (`cache_id`, `ratings`) SELECT `cache_rating`.`cache_id`, COUNT(`cache_rating`.`cache_id`) AS `ratings` FROM `cache_rating` INNER JOIN &topLocationCaches ON `cache_rating`.`cache_id`=&topLocationCaches.`cache_id` INNER JOIN `caches` ON `cache_rating`.`cache_id`=`caches`.`cache_id` WHERE `cache_rating`.`user_id`!=`caches`.`user_id` GROUP BY `cache_rating`.`cache_id`");

				sql_slave("INSERT INTO &topResult (`idx`, `cache_id`, `ratings`, `founds`) 
				     SELECT SQL_SMALL_RESULT (&topRatings.`ratings`+1)*(&topRatings.`ratings`+1)/(IFNULL(`stat_caches`.`found`, 0)/10+1)*100 AS `idx`, 
				            &topRatings.`cache_id`,
				            &topRatings.`ratings`, 
				            IFNULL(`stat_caches`.`found`, 0) AS founds
				       FROM &topRatings
				 INNER JOIN `caches` ON &topRatings.`cache_id`=`caches`.`cache_id`
				  LEFT JOIN `stat_caches` ON `stat_caches`.`cache_id`=`caches`.`cache_id`
				   ORDER BY `idx` DESC LIMIT 15");

				if (sql_value_slave("SELECT COUNT(*) FROM &topResult", 0) > 10)
				{
					$min_idx = sql_value_slave("SELECT `idx` FROM &topResult ORDER BY idx DESC LIMIT 9, 1", 0);
					sql_slave("DELETE FROM &topResult WHERE `idx`<'&1'", $min_idx);
				}

				$rsCaches = sql_slave("SELECT SQL_BUFFER_RESULT &topResult.`idx`, 
				                        &topResult.`ratings`, 
				                        IFNULL(`stat_caches`.`found`, 0) AS `founds`, 
				                        &topResult.`founds` AS `foundAfterRating`, 
				                        &topResult.`cache_id`, 
				                        `caches`.`name`, 
				                        `caches`.`wp_oc` AS `wpoc`, 
				                        `user`.`username`,
				                        `user`.`user_id` AS `userid`
				                   FROM &topResult
				             INNER JOIN `caches` ON &topResult.`cache_id`=`caches`.`cache_id` 
				             INNER JOIN `user` ON `caches`.`user_id`=`user`.`user_id` 
				              LEFT JOIN `stat_caches` ON `stat_caches`.`cache_id`=`caches`.`cache_id`
				               ORDER BY `idx` DESC");

				$items = array();
				while ($rCaches = sql_fetch_assoc($rsCaches))
					$items[] = $rCaches;
				sql_free_result($rsCaches);

				$adm3Group['items'] = $items;

				if (count($adm3Group['items']) > 0)
					$adm1Group['adm3'][] = $adm3Group;

				$adm3Group = array();
			}
			sql_free_result($rsAdm3);

			if (isset($adm1Group['adm3']) && count($adm1Group['adm3']) > 0)
				$tops[] = $adm1Group;

			$adm1Group = array();
		}
		sql_free_result($rsAdm1);

		sql_drop_temp_table_slave('topLocationCaches');
		sql_drop_temp_table_slave('topRatings');
		sql_drop_temp_table_slave('topResult');

		$tpl->assign('tops', $tops);
	}

	$tpl->display();
?>