
/*
delete from cache_type where id = '11';
delete from cache_type where id = '12';
delete from cache_type where id = '13';
delete from cache_type where id = '14';
*/


/* map
pl-type de-type description
1       1       Other
2       2       Trad
3       3       Multi
4       4       Virt
5       5       Webcam
6       6       Event
7       7       Quiz
        8       Math/Physics-Cache
8       9       Moving
        10      Drive-In
9       11      Podcast
10      12      Educache
11      13      Challenge
12      14      Guestbook

create temporary table type_conversion (
  pltype tinyint(3) unsigned NOT NULL,
  detype tinyint(3) unsigned NOT NULL );

insert into type_conversion ( pltype, detype ) values ( 1, 1 );
insert into type_conversion ( pltype, detype ) values ( 2, 2 );
insert into type_conversion ( pltype, detype ) values ( 3, 3 );
insert into type_conversion ( pltype, detype ) values ( 4, 4 );
insert into type_conversion ( pltype, detype ) values ( 5, 5 );
insert into type_conversion ( pltype, detype ) values ( 6, 6 );
insert into type_conversion ( pltype, detype ) values ( 7, 7 );
insert into type_conversion ( pltype, detype ) values ( 8, 9 );
insert into type_conversion ( pltype, detype ) values ( 9, 11 );
insert into type_conversion ( pltype, detype ) values ( 10, 12 );
insert into type_conversion ( pltype, detype ) values ( 11, 13 );
insert into type_conversion ( pltype, detype ) values ( 12, 14 );

(select detype from type_conversion where pltype = `type`) as `type`
*/


INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('11', 'Podcache', '1043', '11', 'Pod', 'Podcast', 'Podcast', 'cache/podcache.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('12', 'Educache', '1044', '12', 'Edu', 'Educache', 'Educache', 'cache/edu.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('13', 'Challenge', '1045', '13', 'Chlg.', 'Challenge', 'Challenge', 'cache/challenge.png');
INSERT INTO `cache_type` (`id`, `name`, `trans_id`, `ordinal`, `short`, `de`, `en`, `icon_large`) VALUES ('14', 'Guestbook', '1046', '14', 'Guest', 'Guestbook', 'Guestbook', 'cache/guestbook.png');

INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('11', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('12', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('13', '3');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '1');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '2');
INSERT INTO `cache_logtype` (`cache_type_id`, `log_type_id`) VALUES ('14', '3');


