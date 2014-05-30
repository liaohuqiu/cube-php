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
        $this->_data = self::mergeFuncArgsData($argv, $this->_data);
        return $this;
    }

    public static function mergeFuncArgsData($argv, $data)
    {
        if (count($argv) == 1)
        {
            $data = array_merge($data, $argv[0]);
        }
        else
        {
            $data[$argv[0]] = $argv[1];
        }
        return $data;
    }
}
