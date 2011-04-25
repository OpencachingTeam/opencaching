<?php

class Rss_FeedGenerator
{
  private $feed_data;
  private $translator;

  public function __construct($feed_data, $translator)
  {
    $this->feed_data = $feed_data;
    $this->translator = $translator;
  }

  public function outputFeed()
  {
    header('Content-type: application/rss+xml; charset="utf-8"');

    $content = $this->getRssHeader();

    $rs = $this->feed_data->getItems();

    while ($r = sql_fetch_array($rs))
    {
      $content .= $this->getItemLine($r);
    }

    mysql_free_result($rs);

    $content .= "
  </channel>
</rss>";

    echo $content;
  }

  function getRssHeader()
  {
    $content = $this->translator->substitute('<?xml version="1.0"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>%site_title% - {title}</title>
    <description>{description}</description>
    <link>%site_url%</link>
    <atom:link href="%site_url%/rss/{atom_link}" rel="self" type="application/rss+xml" />');

    $content = str_replace('{title}', htmlspecialchars($this->feed_data->title), $content);
    $content = str_replace('{description}', htmlspecialchars($this->feed_data->description), $content);
    $content = str_replace('{atom_link}', htmlspecialchars($this->feed_data->atomLink), $content);

    return $content;
  }

  function getItemLine($r)
  {
    $itemline = "
      <item>
        <title>{title}</title>
        <description><![CDATA[{description}]]></description>
        <link>{link}</link>
        {enclosure}
        <pubDate>{pub_date}</pubDate>
        <guid>{item_id}</guid>
      </item>";

    $itemline = str_replace('{title}', htmlspecialchars($this->feed_data->getItemTitle($r)), $itemline);
    $itemline = str_replace('{description}', $this->feed_data->getItemDescription($r), $itemline);
    $itemline = str_replace('{link}', $this->feed_data->getItemLink($r), $itemline);
    $itemline = str_replace('{enclosure}', $this->feed_data->getItemEnclosure($r), $itemline);
    $itemline = str_replace('{pub_date}', date("r", $this->feed_data->getItemDate($r)), $itemline);
    $itemline = str_replace('{item_id}', $this->feed_data->getItemId($r), $itemline);

    return $itemline;
  }
}

?>
