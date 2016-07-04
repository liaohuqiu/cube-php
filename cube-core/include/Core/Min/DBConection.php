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
        if (null == $db || !$db || !mysqli_ping($db))
        {
            // to handle MySql Server has gone away.
            if (null != $db)
            {
                mysqli_close($db);
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
        $db = $this->_dbConfInfo['db'];
        $cnt = false;
        for ($i = 0; $i < 3; $i++)
        {
            $cnt = @mysqli_connect($host, $user, $pwd, $db);
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
        $this->_lastDB = $db;
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
            $ret = mysqli_select_db($this->_connection, $dbName);
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
        $encoding = mysqli_character_set_name($this->_connection);
        if($encoding != $charset)
        {
            mysqli_set_charset($this->_connection, $charset);
        }
    }

    public function query($sql)
    {
        $this->_activate();

        if (!\MCore_Tool_Env::isProd())
        {
            MCore_Tool_Log::addDebugLog("query", $this->_dbConfInfo['key'] . ' ' . $sql);
        }
        $sResult = mysqli_query($this->_connection, $sql);
        if(false === $sResult)
        {
            throw new MCore_Min_DBException(mysqli_error($this->_connection));
        }

        $data = array();
        $rownum = 0;
        $affectedRowNumber = 0;

        // insert will return true, can not use mysqli_fetch_array to fetch array result
        if (true !== $sResult)
        {
            while($row = mysqli_fetch_array($sResult, MYSQLI_ASSOC))
            {
                $data[] = $row;
                $rownum ++;
            }
        }

        $mysqlInsertId = mysqli_insert_id($this->_connection);
        $affectedRowNumber = mysqli_affected_rows($this->_connection);
        $result = new MCore_Dao_Result($data, $rownum, $mysqlInsertId, $affectedRowNumber);

        $actions = array('SELECT', 'SHOW', 'EXPLAIN', 'DESCRIBE');
        $sqlPre = strtoupper(trim(substr($sql, 0, 20)));
        foreach ($actions as $act)
        {
            if (strpos($sqlPre, $act) === 0)
            {
                mysqli_free_result($sResult);
                break;
            }
        }
        $result['error'] = mysqli_error($this->_connection);
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
