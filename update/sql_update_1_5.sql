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
PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");


CREATE TABLE IF NOT EXISTS `tan_themes` (
  `id` int(11) NOT NULL,
  `theme_name` varchar(255) NOT NULL,
  `theme_version` varchar(255) NOT NULL DEFAULT '1',
  `theme_json_data` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
