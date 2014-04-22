<?php
/**
 * table schema info of a sigle table
 *
 * @author      huqiu
 */
class MCore_Min_TableSchema
{
    protected $kind;
    private $_info;
    private $_priKeys;
    private $_keys;
    private static $_cacheList = array();

    function __construct($kind)
    {
        if (empty($kind))
        {
            throw new MCore_Min_DBException('Table kind can not be empty.');
        }
        $this->kind = $kind;
    }

    public static function create($kind)
    {
        $kind = trim($kind);
        if (!isset(self::$_cacheList[$kind]))
        {
            self::$_cacheList[$kind] = new MCore_Min_TableSchema($kind);
        }
        return self::$_cacheList[$kind];
    }

    public function query($dbMan = null)
    {
        if(!$this->_info)
        {
            !$dbMan && $dbMan = MCore_Proxy_DBMan::create();
            $kind = $this->kind;
            $ret = $dbMan->sQuery($kind, 1, "desc $kind");

            $tableFields = array();
            $primaryKeys = array();
            foreach($ret["data"] as $item)
            {
                $field = $item['Field'];
                //Ö÷¼ü
                if($item['Key'] == 'PRI')
                {
                    $this->_priKeys[] = $field;
                }
                //ËùÓÐ¼ü
                $this->_keys[] = $field;
            }
            $ex = new Exception();
            $this->_info = $ret["data"];
        }
        return $this;
    }

    public function getKeys()
    {
        return $this->_keys;
    }

    public function getPriKeys()
    {
        return $this->_priKeys;
    }

    public function getInfo()
    {
        return $this->_info;
    }
}
?>
