<?php
/**
 *   Êý¾Ý
 *
 * @author      huqiu
 */
class MCore_Util_DataContainer
{
    private $_data = array();

    function getData()
    {
        $argv = func_get_args();
        $argc = func_num_args();
        if ($argc == 0)
        {
            return $this->_data;
        }
        else
        {
            return $this->_data[$argv[0]];
        }
    }

    function setData()
    {
        $argc = func_num_args();
        $argv = func_get_args();
        if ($argc == 1)
        {
            $this->_data = array_merge($this->_data, $argv[0]);
        }
        else
        {
            $this->_data[$argv[0]] = $argv[1];
        }
        return $this;
    }
}
