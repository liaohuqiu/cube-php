<?php
/**
 *
 * @author      huqiu
 */
class MCore_Web_JsonTool
{
    protected $data = array();

    public function setData()
    {
        $args = func_get_args();
        return $this->setFuncArgsData($args);
    }

    public function setFuncArgsData($args)
    {
        if (count($args) == 1)
        {
            $this->data = array_merge($this->data, $args[0]);
        }
        else
        {
            $this->data[$args[0]] = $args[1];
        }
        return $this;
    }

    public static function encode($data)
    {
        $data = MCore_Str_JSON::stringify($data);
        return $data;
    }
}
