<?php
/**
 * db info
 *
 * @author srain
 */
class MCore_Min_DBInfo extends MCore_Util_ArrayLike
{
    private static $_KEYS = array('h', 'p', 'u', 'P', 'db');
    public function __construct($h, $u, $p, $P, $db, $key)
    {
        if (!$h || !$u)
        {
            // necessary
            throw new MCore_Min_DBException(__CLASS__ . ' __construct() error: no specific param');
        }
        !$P && $P = 3306;
        $data = array();
        $data['h'] = $h;
        $data['u'] = $u;
        $data['p'] = $p;
        $data['db'] = $db;
        $data['P'] = $P;
        $data['key'] = $key;
        parent::__construct($data, array());
    }

    public static function create($conf = array())
    {
        static $infoList;
        if (!$infoList)
        {
            $infoList = array();
        }
        $key = self::getUniqueKey($conf);
        if (!isset($infoList[$key]))
        {
            $infoList[$key] = new MCore_Min_DBInfo($conf['h'], $conf['u'], $conf['p'], $conf['P'], $conf['db'], $key);
        }
        return $infoList[$key];
    }

    public function getConnectionStr($pre = 'mysql')
    {
        $info = $this->_data;
        $h = $info['h'];
        $P = $info['P'];
        $u = $info['u'];
        $p = $info['p'];
        $db = $info['db'];

        $str = "-h$h -P$P -u$u -p$p $db";
        if ($pre)
        {
            $str = $pre . ' ' . $str;
        }
        return $str;
    }

    public function getHostAndPortStr()
    {
        $port = $this->_data["P"];
        $host = $this->_data["h"];
        if(!empty($port) && strpos($host, ":") === false)
        {
            $host = "$host:$port";
        }
        return $host;
    }

    /**
     * A connection is identified by {host, port, user, password, db}
     */
    public static function getUniqueKey($data)
    {
        $cacheKey = '';
        $strs = array();
        foreach (self::$_KEYS as $k)
        {
            $v = $data[$k];
            $strs[] = $v;
        }
        return implode('_', $strs);
    }
}