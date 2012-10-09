<?php
	$this->add_table('spoton_saved_search', "
		qid int(10) unsigned NOT NULL auto_increment,
		blog_id bigint(20) unsigned NOT NULL DEFAULT '1',
		title text COLLATE 'utf8_general_ci' NOT NULL,
		query longtext COLLATE 'utf8_general_ci' NOT NULL,
		PRIMARY KEY  (qid),
		KEY blog_id (blog_id)
	", array('engine' => 'MyISAM', 'install_method' => 'install'));

	$this->add_table('spoton_sff_fields', "
		fid int(10) unsigned NOT NULL auto_increment,
		blog_id bigint(20) unsigned NOT NULL DEFAULT '1',
		static tinyint(2) unsigned NOT NULL DEFAULT '0',
		title varchar(255) COLLATE 'utf8_general_ci' NOT NULL,
		hide tinyint(2) unsigned NOT NULL DEFAULT '0',
		filter tinyint(2) unsigned NOT NULL DEFAULT '0',
		def_value varchar(255) COLLATE 'utf8_general_ci' NOT NULL DEFAULT '0',
		PRIMARY KEY  (fid),
		UNIQUE KEY title (title),
		KEY blog_id (blog_id)
	", array('engine' => 'MyISAM', 'install_method' => 'install'));

	$this->add_table('spoton_sff_values', "
		vid bigint(20) unsigned NOT NULL auto_increment,
		fid int(10) unsigned NOT NULL,
		value varchar(255) COLLATE 'utf8_general_ci' NOT NULL,
		`order` int(10) unsigned NOT NULL DEFAULT '0',
		PRIMARY KEY  (vid),
		UNIQUE KEY fid_value (fid, value),
		KEY fid (fid),
		KEY `order` (`order`)
	", array('engine' => 'MyISAM', 'install_method' => 'install'));
?>