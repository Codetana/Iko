/*
    Change the tan_ prefix to your prefix!!!!
 */

CREATE TABLE IF NOT EXISTS `tan_user_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `profile_owner` int(11) NOT NULL,
  `writer` int(11) NOT NULL,
  `comment` text NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `tan_user_visitors` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`profile_owner` int(11) NOT NULL,
`visitor` int(11) NOT NULL,
`timestamp` int(11) NOT NULL,
PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `tan_themes` (
  `id` int(11) NOT NULL,
  `theme_name` varchar(255) NOT NULL,
  `theme_version` varchar(255) NOT NULL DEFAULT '1',
  `theme_json_data` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

ALTER TABLE `tan_forum_posts` ADD FULLTEXT search(post_title, post_content);

ALTER TABLE `tan_generic`
  ADD COLUMN `number_subs` INT(3) DEFAULT 3  NOT NULL AFTER `post_merge`;

ALTER TABLE `tan_themes` CHANGE `theme_json_data` `theme_json_data` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `tan_users` CHANGE `chosen_theme` `chosen_theme` INT NOT NULL DEFAULT '0';

CREATE TABLE `tan_labels`(
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `node_id` INT(11) NOT NULL,
  `label` VARCHAR(1000) NOT NULL,
  PRIMARY KEY (`id`))ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `tan_forum_posts`
  ADD COLUMN `label` INT(11) NOT NULL AFTER `watchers`;