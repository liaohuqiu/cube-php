CREATE TABLE IF NOT EXISTS sys_sever_setting (
    sid int(11) NOT NULL auto_increment,
    group_key varchar(50) NOT NULL default '',
    master_sid int(11) NOT NULL,
    host varchar(255) NOT NULL,
    port int(10) unsigned NOT NULL,
    user varchar(32) NOT NULL,
    passwd varchar(32) NOT NULL,
    active tinyint(1) NOT NULL default '1',
    remark text NOT NULL,
    PRIMARY KEY  (sid),
    UNIQUE KEY host (host,port),
    KEY master_sid (master_sid),
    KEY group_key (group_key)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS sys_kind_setting (
    kind varchar(64) NOT NULL,
    table_prefix varchar(64) NOT NULL,
    table_num int(11) NOT NULL,
    db_name varchar(20) NOT NULL,
    app_name varchar(20) NOT NULL,
    id_field varchar(64) NOT NULL,
    remark text NOT NULL,
    enable tinyint(1) NOT NULL default '1',
    PRIMARY KEY  (kind),
    UNIQUE KEY table_prefix (table_prefix)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS sys_table_setting (
    kind varchar(64) NOT NULL,
    no int(11) NOT NULL,
    sid int(11) NOT NULL,
    UNIQUE KEY kind (kind,no)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
