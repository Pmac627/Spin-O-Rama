CREATE TABLE IF NOT EXISTS `captains` (
  `captain_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT 'Captain ID Num',
  `email` varchar(60) NOT NULL COMMENT 'Captain Email (60 char max)',
  PRIMARY KEY (`captain_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `combinations` (
  `combination_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Combination ID Num',
  `combination` varchar(100) NOT NULL COMMENT 'Combination (##-##-##-##-##)',
  PRIMARY KEY (`combination_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `prizes` (
  `prize_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Prize ID Num',
  `prize_name` varchar(50) NOT NULL COMMENT 'Prize Name (50 char max)',
  `prize_quantity` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Prize Quantity (Total Per Week)',
  `prize_description` varchar(1000) NOT NULL COMMENT 'Prize Description (1000 char max)',
  `prize_icon` varchar(60) NOT NULL COMMENT 'Prize Icon (60 char max)',
  `prize_frequency` int(11) NOT NULL COMMENT 'Prize Frequency (A specific Roll to win on)',
  `prize_active` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Prize Active (1 = Yes)',
  `prize_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Prize Delete (1 = Yes)',
  PRIMARY KEY (`prize_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `prize_combination` (
  `prize_id` int(11) NOT NULL COMMENT 'Prize ID Num',
  `combination_id` int(11) NOT NULL COMMENT 'Combination ID Num'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relational Table: prizes->combinations';

CREATE TABLE IF NOT EXISTS `prize_location` (
  `prize_id` int(11) NOT NULL COMMENT 'Prize ID Num',
  `switchboard_id` tinyint(4) NOT NULL COMMENT 'Switchboard ID Num'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Relational Table: prizes->switchboard (location)';

CREATE TABLE IF NOT EXISTS `switchboard` (
  `sb_id` smallint(6) NOT NULL AUTO_INCREMENT COMMENT 'Switchboard ID Num',
  `sb_name` varchar(30) NOT NULL COMMENT 'Slot Machine Name (location)',
  `sb_code` varchar(5) NOT NULL COMMENT 'Switchboard Code (5 char max)',
  `sb_codecount` int(11) NOT NULL DEFAULT '1' COMMENT 'Switchboard Code Count (Increment Per Prize Won)',
  `sb_open` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Switchboard Open (1 = Yes)',
  `sb_winall` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'Switchboard Win All (1 = Yes)',
  `sb_start` varchar(12) NOT NULL DEFAULT 'TU-0900' COMMENT 'Switchboard Start (WEEK-ARMYTIME)',
  `sb_end` varchar(12) NOT NULL DEFAULT 'TU-1000' COMMENT 'Switchboard End (WEEK-ARMYTIME)',
  PRIMARY KEY (`sb_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'User ID Num',
  `user_email` varchar(60) NOT NULL COMMENT 'User Email (60 char max)',
  `user_ip` varchar(20) NOT NULL COMMENT 'User IP Address (20 char max)',
  `user_date` varchar(30) NOT NULL COMMENT 'User Last Spin Date (30 char max)',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `winners` (
  `user_id` int(11) NOT NULL COMMENT 'User ID Num',
  `prize_id` int(11) NOT NULL COMMENT 'Prize ID Num',
  `date` varchar(30) NOT NULL COMMENT 'Date Of Win (30 char max)',
  `winner_code` varchar(10) NOT NULL COMMENT 'Winner Code (XXXX######)',
  `winner_fullname` varchar(60) NOT NULL COMMENT 'Winner Full Name (60 char max)'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;