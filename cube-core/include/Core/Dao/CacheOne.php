<?php
/**
 *
 * @category    kxm
 * @package     core
 * @subpackage  data
 * @author      huqiu
 */
class MCore_Data_CacheOne
{
    static $work = true;

    //table cached tag list
    private static $_cachedTableList = array();
    private static $_whereFieldsInfoList = array();
    const SELETED_ALL_KEY = 1;

    protected $dbman;

    public function __construct($dbMan = null)
    {
        if ($dbMan == null)
        {
            $dbMan = new MCore_Proxy_DBMan();
        }
        $this->dbman = $dbMan;
    }

    public static function create($appModuleName = "", $dbMan = null)
    {
        return new MCore_Data_CacheOne($appModuleName, $dbMan);
    }

    /**
     * check if the table is using cache
     */
    public function isCached($kind)
    {
        if (!isset(self::$_cachedTableList[$kind]))
        {
            $conf = $this->_getTableInfo($kind, false);
            self::$_cachedTableList[$kind] = $conf ? true : false;
        }
        return self::$_cachedTableList[$kind];
    }

    public function invalidateInfoList($kind, $infoList)
    {
        if (!$this->isCached($kind) || !is_array($infoList))
        {
            return false;
        }
        $whereFieldsList = array();
        foreach ($infoList as $infoItem)
        {
            $tempList = $this->getWhereFieldsList($kind, $infoItem);
            $whereFieldsList = array_merge($whereFieldsList, $tempList);
        }
        $this->_invalidate($kind, $whereFieldsList);
    }

    public function invalidateInfo($kind, $info)
    {
        if (!$this->isCached($kind) || !is_array($info))
        {
            return false;
        }

        $whereFieldsList = $this->getWhereFieldsList($kind, $info);
        $this->_invalidate($kind, $whereFieldsList);
    }

    public function getWhereFieldsList($kind, $info)
    {
        if (!$this->isCached($kind) || !is_array($info))
        {
            return false;
        }

        $whereFieldsConfs = $this->_getWhereFieldsConfs($kind);
        $whereFieldsList = $this->_getInvalidateWhereFieldsList($kind, $info, $whereFieldsConfs);
        return $whereFieldsList;
    }

    private function _getInvalidateWhereFieldsList($kind, $info, $whereFieldsConfs)
    {
        $whereFieldsList = array();
        foreach ($whereFieldsConfs as $whereFieldsConf)
        {
            $whereFields = array();
            foreach($whereFieldsConf as $key)
            {
                if($key == self::SELETED_ALL_KEY)
                {
                    $whereFields[$key] = 1;
                }
                else
                {
                    if(!isset($info[$key]))
                    {
                        throw new Exception("there is not value which named $key in argument name" . ' $info');
                    }
                    $whereFields[$key] = $info[$key];
                }
            }
            $whereFieldsList[] = $whereFields;
        }
        return $whereFieldsList;
    }

    private function _invalidate($kind, $whereFieldList)
    {
        //try to remove the duplicate whereFiled
        $mcKeys = array();
        foreach ($whereFieldList as $whereField)
        {
            $mcKey = $this->_getMcCaheKey($kind, $whereField);
            if(!isset($mcKeys[$mcKey]))
            {
                $mcKeys[$mcKey] = $mcKey;
            }
        }

        foreach($mcKeys as $mcKey => $mcKey)
        {
            $this->MCore_Proxy_MCache->delete($mcKey);
        }
    }

    public function getValueOne($kind, $whereField, $selectField = array("*"), $cacheEmpty = true)
    {
        $data = $this->getValueList($kind, $whereField, $selectField, $cacheEmpty);
        if (is_array($data) && isset($data[0]))
        {
            return $data[0];
        }
        else
        {
            return array();
        }
    }

    function getValueList($kind, $whereField, $selectField = array("*"), $cacheEmpty = true)
    {
        $this->_checkWhereFields($kind, $whereField);
        foreach ($whereField as $key=>$val)
        {
            if(!in_array("*", $selectField) && !in_array($key, $selectField) && !is_numeric($key))
            {
                $selectField[] = $key;
            }
        }
        assert($kind);
        assert(is_array($whereField) && !empty($whereField));

        $mcKey = $this->_getMcCaheKey($kind, $whereField);

        if (self::$work)
        {
            $cache = $this->MCore_Proxy_MCache->getObj($mcKey);

            if (($cacheEmpty && $cache !== false) || !empty($cache))
            {
                return $cache;
            }
        }

        $ret = $this->MCore_Data_One->select($kind, $selectField, $whereField, "", 0, 0, -1);
        $data = $ret["data"];

        if (!empty($data))
        {
            $this->MCore_Proxy_MCache->setObj($mcKey, $data, 86400);
        }
        else
        {
            $data = array();
            if($cacheEmpty)
            {
                $this->MCore_Proxy_MCache->setObj($mcKey, $data, 600);
            }
        }
        return $data;
    }

    function getValuesOne($kind, $whereFieldList, $selectField = array("*"), $cacheEmpty = true)
    {
        $list = array();
        $orginList = $this->getValuesList($kind, $whereFieldList, $selectField, $cacheEmpty);
        foreach ($orginList as $valueKey => $infoList)
        {
            $firstInfo = array();
            if(is_array($infoList) && isset($infoList[0]))
            {
                $firstInfo = $infoList[0];
            }
            $list[$valueKey] = $firstInfo;
        }
        return $list;
    }

    function getValuesList($kind, $whereFieldList, $selectField = array("*"), $cacheEmpty = true)
    {
        assert(is_array($whereFieldList) && !empty($whereFieldList));

        //try to remove the duplicate whereFiled
        $mcKeys = array();
        $newWhereFieldList = array();
        foreach ($whereFieldList as $whereField)
        {
            //check each whereField
            $this->_checkWhereFields($kind, $whereField);

            $mcKey = $this->_getMcCaheKey($kind, $whereField);
            if (!in_array($mcKey, $newWhereFieldList))
            {
                $newWhereFieldList[] = $whereField;
                $mcKeys[] = $mcKey;
            }
        }
        $whereFieldList = $newWhereFieldList;

        if (self::$work)
        {
            $caches = $this->MCore_Proxy_MCache->getMultiObj($mcKeys);
        }
        else
        {
            $caches = array();
        }
        //try to fetch value from cache
        $result = array();
        $noCacheMcKeys = array();
        $noCacheWhereFieldList = array();

        foreach ($whereFieldList as $whereField)
        {
            $mcKey = $this->_getMcCaheKey($kind, $whereField);
            if (($cacheEmpty && isset($caches[$mcKey])) || !empty($caches[$mcKey]))
            {
                $result[$mcKey] = $caches[$mcKey];
            }
            else
            {
                $noCacheMcKeys[] = $mcKey;
                $noCacheWhereFieldList[] = $whereField;
            }
        }

        //fetch value from db which are not in cache
        if (!empty($noCacheWhereFieldList))
        {
            $dbList = $this->_getListFromDb($kind, $noCacheWhereFieldList, $selectField);
            $dbList = array_combine($noCacheMcKeys, $dbList);

            foreach ($dbList as $mcKey => $info)
            {
                if (!empty($info))
                {
                    $this->MCore_Proxy_MCache->setObj($mcKey, $info, 86400);
                }
                else
                {
                    if ($cacheEmpty)
                    {
                        $this->MCore_Proxy_MCache->setObj($mcKey, $info, 600);
                    }
                }
                $result[$mcKey] = $info;
            }
        }
        return $result;
    }

    private function _getListFromDb($kind, $whereFieldList, $selectField = array("*"))
    {
        //make sure all the fields will be seleted
        $fields = array();
        foreach ($whereFieldList as $whereField)
        {
            $fields = array_merge($fields, array_keys($whereField));
        }
        $fields = array_unique($fields);
        foreach ($fields as $fieldKey)
        {
            if(!in_array("*", $selectField) && !in_array($fieldKey, $selectField))
            {
                $selectField[] = $fieldKey;
            }
        }

        $len = count($whereFieldList);
        $rawList = array();
        $amountEachTime = 50;
        for ($i = 0; $i < $len; $i += $amountEachTime)
        {
            $subWhereFieldList = array_slice($whereFieldList, $i, $amountEachTime);
            $sqls = array();
            foreach($subWhereFieldList as $whereField)
            {
                $sqls[] = MCore_Tool_Sql::select($kind, $selectField, $whereField, "", 0, 0);
            }
            $ret = $this->dbman->mQuery($kind, 1, $sqls, -1);
            $rawList = array_merge($rawList, MCore_Tool_Array::getFields($ret, "data"));
        }

        if (!$rawList)
        {
            return array();
        }
        return $rawList;
    }

    private function _getTableInfo($kind, $forceCheckConfig = true)
    {
        $data = MCore_Tool_Conf::getDataConfig('base', $forceCheckConfig);
        $conf = $data['cachedSingleTable'];
        if (!$conf)
        {
            return false;
        }
        else
        {
            if(!isset($conf[$kind]))
            {
                return false;
            }
            return $conf[$kind];
        }
    }

    private function _checkWhereFields($kind, $whereFields)
    {
        sort($keys = array_keys($whereFields), SORT_STRING);
        $whereFieldsConfs = $this->_getWhereFieldsConfs($kind);
        foreach ($whereFieldsConfs as $conf)
        {
            sort($conf, SORT_STRING);
            if ($conf == $keys)
            {
                return $whereFields;
            }
        }
        throw new Exception('this $whereFileds is not in config.');
    }

    private function _getWhereFieldsConfs($kind)
    {
        if (!isset(self::$_whereFieldsInfoList[$kind]))
        {
            $conf = $this->_getTableInfo($kind, true);
            $whereFieldsConfs = $conf['whereFields'];

            if (!$whereFieldsConfs || empty($whereFieldsConfs))
            {
                throw new Exception("the whereFields of table:$kind is not configed");
            }
            self::$_whereFieldsInfoList[$kind] = $whereFieldsConfs;
        }
        return self::$_whereFieldsInfoList[$kind];
    }

    private function _getMcCaheKey($kind, $arr)
    {
        assert($kind);
        assert(!empty($arr));
        assert(is_array($arr));

        $conf = $this->_getTableInfo($kind, true);

        $keyPart = $conf['cacheKeyPart'];
        !$keyPart && $keyPart = "cache0";

        //sort by key
        ksort($arr, SORT_STRING);
        $mcKey = $kind."_$keyPart";

        foreach ($arr as $key=>$value)
        {
            $mcKey .= "_$key"."_$value";
        }

        assert(strlen($mcKey)  < 1000);
        return $mcKey;
    }
}
?>
