-- kind=s_demo_kv
-- table_num=1
-- split_id=k
CREATE TABLE s_demo_kv (
    k varchar(50) NOT NULL default '',
    v blob NOT NULL,
    ctime timestamp NOT NULL default 0,
    mtime timestamp NOT NULL default current_timestamp ON UPDATE current_timestamp,
    UNIQUE KEY k(k)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
