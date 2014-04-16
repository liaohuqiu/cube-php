-- kind={admin_user_table}
-- table_num=1
-- split_id=uid
CREATE TABLE IF NOT EXISTS {admin_user_table} (
    uid int(11) NOT NULL auto_increment PRIMARY KEY,
    user_group tinyint NOT NULL default 0,
    status tinyint NOT NULL default 0,
    email varchar(50) NOT NULL default '',
    pwd varchar(32) NOT NULL default '',
    salt varchar(6) NOT NULL default '',
    token varchar(10) NOT NULL default '',
    is_sysadmin tinyint NOT NULL default 0,
    auth_keys varchar(500) NOT NULL default '',
    data blob NOT NULL default '',
    ctime timestamp NOT NULL default 0,
    mtime timestamp NOT NULL default current_timestamp ON UPDATE current_timestamp,
    UNIQUE KEY (email),
    KEY (email, ctime)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
