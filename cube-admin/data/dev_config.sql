CREATE TABLE IF NOT EXISTS sys_db_info (
    sid int(11) NOT NULL auto_increment,
    group_key varchar(50) NOT NULL default '',
    master_sid int(11) NOT NULL,
    cluster_index int(11) NOT NULL,
    host varchar(255) NOT NULL,
    port int(10) unsigned NOT NULL,
    user varchar(32) NOT NULL,
    passwd varchar(200) NOT NULL,
    db_name varchar(32) NOT NULL,
    charset varchar(32) NOT NULL,
    active tinyint(1) NOT NULL default '1',
    remark text NOT NULL,
    PRIMARY KEY  (sid),
    UNIQUE KEY host (host, port, user, db_name),
    KEY master_sid (master_sid),
    KEY group_key (group_key)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS sys_table_info (
    name varchar(64) NOT NULL,
    table_num int(11) NOT NULL,
    db_group varchar(20) NOT NULL,
    id_field varchar(64) NOT NULL,
    remark text NOT NULL,
    PRIMARY KEY  (name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
