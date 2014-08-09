<?php
class MCore_Web_ViewVar implements ArrayAccess, Iterator, Countable {

    private $_data;

    public function __construct($data)
    {
        $this->_data = $data;
    }

    public function __toString()
    {
        return htmlspecialchars((string)$this->_data);
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function raw($var_name = false)
    {
        if (!$var_name)
        {
            return $this->_data;
        }
        return $this->_data[$var_name];
    }

    public function get($var_name)
    {
        if (is_array($this->_data[$var_name]))
        {
            return new MCore_Web_ViewVar($this->_data[$var_name]);
        }
        return $this->_data[$var_name];
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new Exception('You must specify a key to this value');
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function count()
    {
        return count($this->_data);
    }

    public function current()
    {
        if (is_array(current($this->_data)))
        {
            return new MCore_Web_ViewVar(current($this->_data));
        }
        else
        {
            return current($this->_data);
        }
    }

    public function key()
    {
        return key($this->_data);
    }

    public function next()
    {
        return next($this->_data);
    }

    public function rewind()
    {
        reset($this->_data);
    }

    public function valid()
    {
        return current($this->_data) !== false;
    }

    public function o($key, $filter = true)
    {
        if ($filter)
        {
            echo htmlspecialchars($this->_data[$key]);
        }
        else
        {
            echo $this->_data[$key];
        }
    }
}
