<?php
class MCore_Util_GetChain implements ArrayAccess, Countable, Iterator
{
    private $data = array();

    function __construct($data = array())
    {
        if (!is_array($data))
        {
            throw new Exception('data is not array');
        }
        $this->data = $data;
    }

    public function get($key)
    {
        if (!isset($this->data[$key]))
        {
            return new MCore_Util_GetChain()
        }
        else
        {
            return new MCore_Util_GetChain($this->data[$key])
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->data[] = $value;
        }
        else
        {
            $this->data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    public function count()
    {
        return count($this->data);
    }

    public function current()
    {
        return new MCore_Util_GetChain(current($this->data));
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        return new MCore_Util_GetChain(next($this->data));
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return current($this->data) !== false;
    }
}
