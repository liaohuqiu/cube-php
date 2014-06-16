<?php
/**
 *
 *  A wapper of conection to mysql server
 *
 *  @author:    huqiu
 */
class MCore_Min_DBConection
{
    private $_dbConfInfo;
    private $_connection;
    private $_lastDB = '';

    public function __construct(MCore_Min_DBInfo $dbConfInfo)
    {
        $this->_dbConfInfo = $dbConfInfo;
    }

    private function _activate()
    {
        $db = $this->_connection;
        if (null == $db || !$db || !mysql_ping($db))
        {
            // to handle MySql Server has gone away.
            if (null != $db)
            {
                mysql_close($db);
            }
            $this->_connect();
            $this->selectDB();
            $this->setCharset();
        }
        return true;
    }

    private  function _connect()
    {
        $host = $this->_dbConfInfo->getHostAndPortStr();
        $user = $this->_dbConfInfo['u'];
        $pwd = $this->_dbConfInfo['p'];
        $cnt = false;
        for ($i = 0; $i < 3; $i++)
        {
            $cnt = @mysql_connect($host, $user, $pwd, true);
            if ($cnt === false)
            {
                continue;
            }
            else
            {
                break;
            }
            if ($i < 2)
            {
                usleep(50000);	//50ms
            }
        }
        if ($i)
        {
            throw new MCore_Min_DBException('Fail to establish connection to Mysql');
        }
        $this->_connection = $cnt;
    }

    public function selectDB($dbName = '')
    {
        !$dbName && $dbName = $this->_dbConfInfo['db'];
        if (!\MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog('query', $this->_dbConfInfo['key'] . ' selectDB: ' . $dbName);
        }
        if (!$dbName)
        {
            return false;
        }
        else
        {
            if ($dbName == $this->_lastDB)
            {
                return $this;
            }
            $this->_lastDB = $dbName;
            if (null == $this->_connection)
            {
                throw new MCore_Min_DBException('Connection has not been initialized.');
            }
            $ret = mysql_select_db($dbName, $this->_connection);
            if (!$ret)
            {
                throw new MCore_Min_DBException('Database is not existent or do not have privilege to access it: ' . $dbName);
            }
            return $this;
        }
    }

    public function setCharset($charset = '')
    {
        !$charset && $charset = $this->_dbConfInfo['charset'];
        if (!$charset)
        {
            return false;
        }
        $encoding = mysql_client_encoding($this->_connection);
        if($encoding != $charset)
        {
            mysql_set_charset($charset, $this->_connection);
        }
    }

    public function query($sql)
    {
        $this->_activate();

        if (!\MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog("query", $this->_dbConfInfo['key'] . ' ' . $sql);
        }
        $sResult = mysql_query($sql, $this->_connection);
        if(false === $sResult)
        {
            throw new MCore_Min_DBException(mysql_error($this->_connection));
        }

        $data = array();
        $rownum = 0;
        $affectedRowNumber = 0;

        // insert will return true, can not use mysql_fetch_array to fetch array result
        if (true !== $sResult)
        {
            while($row = mysql_fetch_array($sResult, MYSQL_ASSOC))
            {
                $data[] = $row;
                $rownum ++;
            }
        }
        $mysqlInsertId = mysql_insert_id();
        $affectedRowNumber = mysql_affected_rows($this->_connection);
        $result = new MCore_Dao_Result($data, $rownum, $mysqlInsertId, $affectedRowNumber);

        $actions = array('SELECT', 'SHOW', 'EXPLAIN', 'DESCRIBE');
        $sqlPre = strtoupper(trim(substr($sql, 0, 20)));
        foreach ($actions as $act)
        {
            if (strpos($sqlPre, $act) === 0)
            {
                mysql_free_result($sResult);
                break;
            }
        }
        $result['error'] = mysql_error();
        return $result;
    }

    private static $_connectList = array();

    public static function get($dbInfo)
    {
        $connection = null;
        if ($dbInfo instanceof MCore_Min_DBInfo)
        {
            $cacheKey = MCore_Min_DBInfo::getUniqueKey($dbInfo->toArray());
        }
        else
        {
            $cacheKey = MCore_Min_DBInfo::getUniqueKey($dbInfo);
        }
        if (!isset(self::$_connectList[$cacheKey]))
        {
            if ($dbInfo instanceof MCore_Min_DBInfo)
            {
                $connection = new MCore_Min_DBConection($dbInfo);
            }
            else
            {
                $dbInfo = MCore_Min_DBInfo::create($dbInfo);
                $connection = new MCore_Min_DBConection($dbInfo);
            }
            self::$_connectList[$cacheKey] = $connection;
        }
        else
        {
            $connection = self::$_connectList[$cacheKey];
        }
        return $connection;
    }
}
