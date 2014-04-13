<?php
/**
 *   simple
 *
 * @author      huqiu
 */
class MCore_Util_SimpleConf
{
    private $_confInfoListByKey;
    private $_confInfoListByCode;
    private $_keyCodeMap = array();

    function __construct($confInfoListMayByKey, $keyFieldName = "", $codeFieldName = "code")
    {
        foreach($confInfoListMayByKey as $key => $info)
        {
            if($keyFieldName)
            {
                $key = $info[$keyFieldName];
            }
            $code = $info[$codeFieldName];
            $this->_confInfoListByKey[$key] = $info;
            $this->_confInfoListByCode[$code] = $info;
            $this->_keyCodeMap[$key] = $code;
        }
    }

    function __get($name)
    {
        if($name == "code")
        {
            $this->$name = $this->_confInfoListByCode;
            return $this->$name;
        }
        else if($name == "key")
        {
            $this->$name = $this->_confInfoListByKey;
            return $this->$name;
        }
        return null;
    }

    function getKeyIndexedList()
    {
        return $this->_confInfoListByKey;
    }

    function getCodeIndexedList()
    {
        return $this->_confInfoListByCode;
    }

    function getKey($code)
    {
        return array_search($code,$this->_keyCodeMap);
    }

    function getCode($key)
    {
        if(!isset($this->_keyCodeMap[$key]))
        {
            return false;
        }
        return $this->_keyCodeMap[$key];
    }

    function getInfoByCode($code)
    {
        $info = $this->_confInfoListByCode[$code];
        return $info;
    }

    function getInfoByKey($key)
    {
        $info = $this->_confInfoListByKey[$key];
        return $info;
    }

    function getFieldByCode($code, $fieldName)
    {
        return $this->_confInfoListByCode[$code][$fieldName];
    }

    function getFieldByKey($key,$fieldName)
    {
        return $this->_confInfoListByKey[$key][$fieldName];
    }

    function getKeyCodeMap()
    {
        return $this->_keyCodeMap;
    }

    function getKeyMapTo($fieldName)
    {
        return MCore_Tool_Array::getFields($this->_confInfoListByKey,$fieldName,true);
    }

    function getKeyMapBy($fieldName)
    {
        return array_flip($this->getKeyMapTo($fieldName));
    }

    function getCodeKeyMap()
    {
        return array_flip($this->_keyCodeMap);
    }

    function getCodeMapTo($fieldName)
    {
        return MCore_Tool_Array::getFields($this->_confInfoListByCode,$fieldName,true);
    }

    function getCodeMapBy($fieldName)
    {
        return array_flip($this->getCodeMapTo($fieldName));
    }
}
?>
