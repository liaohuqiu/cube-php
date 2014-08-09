<?php
/**
 *
 * @category    kxm
 * @package     core
 * @subpackage  util/obj
 * @author      huqiu
 */
class MCore_Util_ArrayLike implements ArrayAccess, Countable, Iterator
{
    protected $data = array();
    private $memberFields;

    function __construct($data = array(), $memberFields = array())
    {
        $memberFields = (array)$memberFields;
        if (!is_array($memberFields))
        {
            throw new Exception('memberFields is not array');
        }
        foreach ($memberFields as $key)
        {
            $this->memberFields[$key] = 1;
        }
        if (!is_array($data))
        {
            throw new Exception('data is not array');
        }
        $this->data = $data;
    }

    protected function toInnerKey($offset)
    {
        return $offset;
    }

    protected function toOutKey($offset)
    {
        return $offset;
    }

    public function offsetExists($offset)
    {
        $offset = $this->toInnerKey($offset);
        if (isset($this->memberFields[$offset]))
        {
            return isset($this->$offset);
        }
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        $offset = $this->toInnerKey($offset);
        if (isset($this->memberFields[$offset]))
        {
            return $this->$offset;
        }
        if (!isset($this->data[$offset]))
        {
            throw new Exception('can not access this key: ' . $offset);
        }
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->data[] = $value;
        }
        else
        {
            $offset = $this->toInnerKey($offset);
            if (isset($this->memberFields[$offset]))
            {
                return $this->$offset = $value;
            }
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        $offset = $this->toInnerKey($offset);
        if (isset($this->memberFields[$offset]))
        {
            unset($this->$offset);
        }
        unset($this->data[$offset]);
    }

    public function count()
    {
        return count($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return $this->toOutKey(key($this->data));
    }

    public function next()
    {
        return next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function __toString()
    {
        return var_export($this->toArray(), true);
    }

    public function fetchOne($where)
    {
        return $this->where($where)->first();
    }

    public function sortBy($fieldName, $sortType = 'desc')
    {
        $this->data = MCore_Tool_Array::sortByField($this->data, $fieldName, $sortType);
        return $this;
    }

    public function where($where)
    {
        $ret = MCore_Tool_Array::where($this->data, $where);
        return new MCore_Util_ArrayLike($ret);
    }

    public function first()
    {
        return reset($this->data);
    }

    public function map($key, $valueKey = null)
    {
        $data = MCore_Tool_Array::list2Map($this->data, $key, $valueKey);
        return new MCore_Util_ArrayLike($data);
    }

    public function getFields($key, $preserveIndex = false)
    {
        $data = MCore_Tool_Array::getFields($this->data, $key, $preserveIndex);
        return $data;
    }
}
