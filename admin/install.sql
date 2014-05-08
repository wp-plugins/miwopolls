	CREATE TABLE IF NOT EXISTS `#__miwopolls_polls` (
		  `id` int(11) unsigned NOT NULL auto_increment,
		  `title` varchar(255) NOT NULL default '',
		  `alias` varchar(255) NOT NULL default '',
		  `checked_out` int(11) NOT NULL default '0',
		  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
		  `published` tinyint(1) NOT NULL default '0',
		  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
		  `publish_down` datetime default '0000-00-00 00:00:00',
		  `params` text NOT NULL,
		  `access` int(11) NOT NULL default '0',
		  `lag` int(11) NOT NULL default '0',
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
		
	CREATE TABLE IF NOT EXISTS `#__miwopolls_options` (
		  `id` int(11) NOT NULL auto_increment,
		  `poll_id` int(11) NOT NULL default '0',
		  `text` text NOT NULL,
		  `link` varchar(255) DEFAULT NULL,
		  `color` varchar(6)  NOT NULL,
		  `ordering` int(11) NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `poll_id` (`poll_id`,`text`(1))
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;
		
	CREATE TABLE IF NOT EXISTS `#__miwopolls_votes` (
		  `id` bigint(20) NOT NULL auto_increment,
		  `date` datetime NOT NULL default '0000-00-00 00:00:00',
		  `option_id` int(11) NOT NULL default '0',
		  `poll_id` int(11) NOT NULL default '0',
		  `ip` int(10) unsigned NOT NULL,
		  `user_id` int(11) DEFAULT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `poll_id` (`poll_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=0;