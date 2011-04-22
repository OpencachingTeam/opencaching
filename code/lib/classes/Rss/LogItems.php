<?php

class Rss_LogItems
{
  public function getItems()
  {
    $rs = sql("SELECT cache_logs.cache_id AS cache_id,
                cache_logs.id AS log_id,
                cache_logs.type AS log_type,
                cache_logs.date AS log_date,
                cache_logs.last_modified AS last_modified,
                cache_logs.text AS log_text,
                cache_logs.text_html AS text_html,
                caches.name AS rss_cache_name,
                user.username AS rss_username,
                user.user_id AS user_id
              FROM ((cache_logs
                INNER JOIN caches ON (caches.cache_id = cache_logs.cache_id))
                INNER JOIN user ON (cache_logs.user_id = user.user_id))
              WHERE `cache_logs`.`cache_id` = `caches`.`cache_id`
                AND `caches`.`status` != 4
                AND `caches`.`status` != 5
                AND `caches`.`status` != 6" 
                .$this->getAdditionalWhere().
            " ORDER BY cache_logs.date_created DESC
              LIMIT 20");

    return $rs;
  }

  protected function getAdditionalWhere()
  {
    return "";
  }
}

?>
