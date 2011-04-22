<?php

class Rss_LogsFeedData extends Rss_FeedData
{
  private $translator;
  private $items;

  public function __construct($translator, $items, $title, $atomLink)
  {
    parent::__construct($title, $translator->translate('New cache logs'), $atomLink);

    $this->translator = $translator;
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items->getItems();
  }

  public function getItemTitle($r)
  {
    $itemTitle = $this->getTitleByLogType($r['log_type']);

    return $this->translator->translateArgs($itemTitle, $r);
  }

  public function getItemDescription($r)
  {
    return $r['log_text'];
  }

  public function getItemLink($r)
  {
    return 'http://www.opencaching.de/' . 'viewcache.php?cacheid='.$r['cache_id'];
  }

  public function getItemDate($r)
  {
    return strtotime($r['log_date']);
  }

  public function getItemId($r)
  {
    return $r['log_id'];
  }

  public function getItemEnclosure($r)
  {
    return '<enclosure url="' . 'http://www.opencaching.de/' . 'rss/'.$r['cache_id'].'.gpx" length="4096" type="application/gpx" />';
  }

  function getTitleByLogType($log_type)
  {
    switch($log_type)
    {
      case Log_Type::Found:
        return '{rss_username} found {rss_cache_name}';

      case Log_Type::NotFound:
        return '{rss_username} did not find {rss_cache_name}';

      case Log_Type::Comment:
        return '{rss_username} wrote a comment on {rss_cache_name}';

      case Log_Type::Attended:
        return '{rss_username} attended {rss_cache_name}';

      case Log_Type::WillAttend:
        return '{rss_username} will attend {rss_cache_name}';

      case Log_Type::Solved:
        return '{rss_username} solved {rss_cache_name}';

      case Log_Type::NotSolved:
        return '{rss_username} did not solve {rss_cache_name}';
    }

    return '{rss_username} unknown log type ' . $log_type . ' {rss_cache_name}';
  }
}

?>
