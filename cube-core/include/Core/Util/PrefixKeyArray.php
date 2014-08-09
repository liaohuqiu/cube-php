<?php
/**
 *
 * @category    kxm
 * @package     core
 * @subpackage  util/obj
 * @author      huqiu
 */
class MCore_Util_PrefixKeyArray extends MCore_Util_ArrayLike
{
    private $_keyIn;
    private $_keyOut;
    private $_keyMap;
    private $_keyMapRevert;

    function __construct($data, $keyMap)
    {
        if (is_array($keyMap))
        {
            $this->_keyMap = $keyMap;
            $this->_keyMapRevert = array_flip($keyMap);
            reset($keyMap);
            $this->_keyIn = key($keyMap);
            $this->_keyOut = current($keyMap);
        }
        if (is_string($keyMap))
        {
            $this->_keyIn = $keyMap;
            $this->_keyOut = '';
        }

        parent::__construct($data);
    }

    protected function toInnerKey($index)
    {
        if (is_numeric($index))
        {
            return $this->_keyIn . $index;
        }
        else
        {
            return $this->_keyIn . $this->_getIndex($index);
        }
    }

    protected function toOutKey($index)
    {
        if (is_numeric($index))
        {
            return $this->_keyOut . $index;
        }
        else
        {
            return $this->_keyOut . $this->_getIndex($index);
        }
    }

    private function _getIndex($key)
    {
        $key = str_split($key);
        $index = '';
        foreach ($key as $char)
        {
            $v = ord($char);
            if ($v >= 48 && $v <=57)
            {
                $index .= $char;
            }
        }
        return $index;
    }


    public function trans()
    {
        $list = array();
        foreach ($this as $key => $value)
        {
            $list[$key] = $value;
        }
        return $list;
    }
}
?>
