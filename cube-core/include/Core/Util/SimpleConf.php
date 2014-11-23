<?php
/**
 *   simple
 *
 * @author      huqiu
 */
class MCore_Util_SimpleConf
{
    private $_confInfoListByKey = array();
    private $_confInfoListByCode = array();
    private $_keyCodeMap = array();
    private $_codeKeyMap = array();

    function __construct($confInfoListMayByKey, $keyFieldName = '', $codeFieldName = 'code')
    {
        foreach ($confInfoListMayByKey as $key => $info)
        {
            if ($keyFieldName)
            {
                $key = $info[$keyFieldName];
            }
            if (!is_array($info))
            {
                $info = array('code' => $info);
            }
            if (!isset($info[$codeFieldName]))
            {
                $code = $key;
            }
            else
            {
                $code = $info[$codeFieldName];
            }
            $this->_confInfoListByKey[$key] = $info;
            $this->_confInfoListByCode[$code] = $info;
            $this->_keyCodeMap[$key] = $code;
            $this->_codeKeyMap[$code] = $key;
        }
    }

    function __get($name)
    {
        if ($name == "code")
        {
            $this->$name = $this->_confInfoListByCode;
            return $this->$name;
        }
        else if ($name == "key")
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

    function checkKey($key)
    {
        if (!isset($this->_keyCodeMap[$key]))
        {
            throw new Exception('key not found: ' . $key);
        }
    }

    function checkCode($code)
    {
        if (!isset($this->_codeKeyMap[$code]))
        {
            throw new Exception('code not found: ' . $code);
        }
    }

    function getKey($code)
    {
        if (!isset($this->_codeKeyMap[$code]))
        {
            throw new Exception('code not found: ' . $code);
        }
        return $this->_codeKeyMap[$code];
    }

    function getCode($key)
    {
        if (!isset($this->_keyCodeMap[$key]))
        {
            throw new Exception('key not found: ' . $key);
        }
        return $this->_keyCodeMap[$key];
    }

    function getInfoByCode($code)
    {
        if (!isset($this->_confInfoListByCode[$code]))
        {
            throw new Exception('code not found: ' . $code);
        }
        $info = $this->_confInfoListByCode[$code];
        return $info;
    }

    function getInfoByKey($key)
    {
        if (!isset($this->_confInfoListByKey[$key]))
        {
            throw new Exception('key not found: ' . $key);
        }
        $info = $this->_confInfoListByKey[$key];
        return $info;
    }

    function getFieldByCode($code, $fieldName)
    {
        if (!isset($this->_codeKeyMap[$code]))
        {
            throw new Exception('code not found: ' . $code);
        }
        if (isset($this->_confInfoListByCode[$code][$fieldName]))
        {
            return $this->_confInfoListByCode[$code][$fieldName];
        }
        return false;
    }

    function getFieldByKey($key, $fieldName)
    {
        if (!isset($this->_keyCodeMap[$key]))
        {
            throw new Exception('key not found: ' . $key);
        }
        if (isset($this->_confInfoListByKey[$key][$fieldName]))
        {
            return $this->_confInfoListByKey[$key][$fieldName];
        }
        return false;
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
        return $this->_codeKeyMap;
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
