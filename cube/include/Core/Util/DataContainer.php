<?php
/**
 *   Êý¾Ý
 *
 * @author      huqiu
 */
class MCore_Util_DataContainer
{
    private $_data = array();

    public function getData()
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

    public function setData()
    {
        $argv = func_get_args();
        return $this->setFuncArgsData($argv);
    }

    public function setFuncArgsData($argv)
    {
        if (count($argv) == 1)
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
