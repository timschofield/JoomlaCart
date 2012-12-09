CREATE TABLE IF NOT EXISTS `#__cart_orders` (
  `id` int(11) NOT NULL auto_increment,
  `reference` varchar(20) NOT NULL default '',
  `partnumber` varchar(20) NOT NULL default '',
  `quantityordered` int(11) NOT NULL default '0',
  `price` decimal(11,2) NOT NULL default '0.00',
  `date` datetime default NULL,
  `hardwareaddress` varchar(45) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `hardwareaddress` (`hardwareaddress`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=152 ;



CREATE TABLE IF NOT EXISTS `#__cart_parameters` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `hardwareaddress` varchar(45) NOT NULL,
  `make` int(11) NOT NULL,
  `makename` varchar(45) default NULL,
  `model` varchar(45) NOT NULL,
  `category` varchar(45) default NULL,
  `year` varchar(45) default NULL,
  `modelname` varchar(100) default NULL,
  `stockid` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  KEY `hardwareaddress` (`hardwareaddress`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

CREATE TABLE IF NOT EXISTS `#__cart_usercustomer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user` varchar(25) NOT NULL,
  `customer` varchar(15) NOT NULL,
  `branch` varchar(15) NULL,
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`),
  KEY `customer` (`customer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Cross reference user to customer' AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__cart_zip` (
  `id` int(11) NOT NULL,
  `country` varchar(10) default NULL,
  `region` varchar(50) default NULL,
  `city` varchar(100) default NULL,
  `zip` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  KEY `zip` (`zip`),
  KEY `country` (`country`,`region`,`city`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
