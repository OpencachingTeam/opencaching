<?php

class Rss_CachesFeedData extends Rss_FeedData
{
  private $translator;
  private $lang;

  public function __construct($translator, $lang)
  {
    parent::__construct($translator->translate('New caches with gpx'), $translator->translate('New caches'), 'newcaches.xml');

    $this->translator = $translator;
    $this->lang = $lang;
  }

  public function getItems()
  {
    $rs = sql('SELECT `caches`.`cache_id` `cache_id`,
                `user`.`user_id` `userid`,
                `caches`.`country` `country`,
                `caches`.`name` `rss_cache_name`,
                `user`.`username` `rss_username`,
                `cache_desc`.`short_desc` `short_desc`,
                `cache_desc`.`desc` `long_desc`,
                `caches`.`date_hidden` `date`
              FROM `user`, `caches`, `cache_desc`
              WHERE ' . Cache_Where::active() .
                'AND `caches`.`user_id`=`user`.`user_id`
                AND `caches`.`cache_id` = `cache_desc`.`cache_id`
                AND `cache_desc`.`language` = \'' . $this->lang . '\'
              ORDER BY `caches`.`last_modified` DESC
              LIMIT 20');

    return $rs;
  }

  public function getItemTitle($r)
  {
    return $this->translator->translateArgs('{rss_cache_name} created by {rss_username}', $r);
  }

  public function getItemDescription($r)
  {
    return htmlspecialchars($r['short_desc']) . '<hr/>' . $r['long_desc'];
  }

  public function getItemLink($r)
  {
    return $this->translator->substitute('%site_url%') . '/viewcache.php?cacheid=' . $r['cache_id'];
  }

  public function getItemDate($r)
  {
    return strtotime($r['date']);
  }

  public function getItemId($r)
  {
    return $r['cache_id'];
  }

  public function getItemEnclosure($r)
  {
    return $this->translator->substitute('<enclosure url="%site_url%/rss/') . $r['cache_id'] . '.gpx" length="4096" type="application/gpx" />';
  }
}

?>
