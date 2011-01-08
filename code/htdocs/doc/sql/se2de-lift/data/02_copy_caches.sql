/* Please note that triggers must be installed before running this script */

/* this is to fool triggers not to update dates */
set @XMLSYNC=1;


/*
delete from caches;
delete from caches_attributes;
delete from cache_desc;
delete from cache_visits;
delete from cache_rating;
delete from cache_ignore;
delete from cache_watches;
delete from cache_logs;
*/

INSERT INTO `caches` (
  `cache_id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `user_id`,
  `name`,
  `longitude`,
  `latitude`,
  `type`,
  `status`,
  `country`,
  `date_hidden`,
  `size`,
  `difficulty`,
  `terrain`,
  `logpw`,
  `search_time`,
  `way_length`,
  `wp_gc`,
  `wp_nc`,
  `wp_oc`,
  `desc_languages`,
  `default_desclang`,
  `date_activate`,
  `need_npa_recalc` )
 select
  `cache_id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `user_id`,
  `name`,
  `longitude`,
  `latitude`,
  `type`,
  `status`,
  `country`,
  `date_hidden`,
  `size`,
  `difficulty`,
  `terrain`,
  `logpw`,
  `search_time`,
  `way_length`,
  `wp_gc`,
  `wp_nc`,
  `wp_oc`,
  `desc_languages`,
  `default_desclang`,
  `date_activate`,
  `need_npa_recalc`
--  `founds`
--  `notfounds`
--  `notes`
--  `images`
--  `last_found`
--  `watcher`
--  `picturescount`
--  `topratings`
--  `ignorer_count`
--  `votes`
--  `score`
--  `mp3count`
--  `solution`
--  `solved`
--  `not_solved`
  from ocpl.caches;

insert into `cache_desc` (
  `id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `cache_id`,
  `language`,
  `desc`,
  `desc_html`,
  `desc_htmledit`,
  `hint`,
  `short_desc` )
 select
  `id`,
  `uuid`,
  `node`,
  `date_created`,
  `last_modified`,
  `cache_id`,
  `language`,
  `desc`,
  `desc_html`,
  `desc_htmledit`,
  `hint`,
  `short_desc`
from ocpl.cache_desc;
  
/* no 60 wheelchair accessible is missing
*/
INSERT INTO `caches_attributes` (
`cache_id`, 
`attrib_id`)
select
`cache_id`, 
`attrib_id`
from ocpl.caches_attributes;

INSERT INTO `cache_visits` (
`cache_id`, 
`user_id_ip`, 
`count`, 
`last_modified` )
select
`cache_id`, 
`user_id_ip`, 
`count`, 
last_visited as `last_modified`
from ocpl.cache_visits;

INSERT INTO `cache_rating` (
`cache_id`, 
`user_id` )
select
`cache_id`, 
`user_id`
from ocpl.cache_rating;

INSERT INTO `cache_ignore` (
`cache_id`, 
`user_id` )
select
`cache_id`, 
`user_id`
from ocpl.cache_ignore;

INSERT INTO `cache_watches` (
`cache_id`, 
`user_id`,
`last_executed` )
select
`cache_id`, 
`user_id`,
`last_executed`
from ocpl.cache_watches;

INSERT INTO `cache_logs` (
`id`,
`uuid`,
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picture`) 
select
`id`,
`uuid`,
`node`,
`date_created`,
`last_modified`,
`cache_id`,
`user_id`,
`type`,
`date`,
`text`,
`text_html`,
`text_htmledit`,
`owner_notified`,
`picturescount` as `picture`
-- `deleted`
-- `mp3count`
-- `hidden`
from ocpl.cache_logs;

set @XMLSYNC=0;
