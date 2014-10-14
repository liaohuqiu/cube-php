<?php
/**
 *  request wrapper
 *
 * @author      huqiu
 */
class MCore_Web_Request
{
    private static $instance;

    private $argv;
    private $path;

    public function __construct($info)
    {
        $this->path = $info['path'];
        $this->argv = $info['argv'];
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public static function create($request_info)
    {
        return self::$instance = new MCore_Web_Request($request_info);
    }

    public function getData($varname, $source = 'r', $type = 'noclean', $default = null)
    {
        if (isset($this->argv[$varname]))
        {
            return $this->argv[$varname];
        }
        return MCore_Tool_Input::clean($source, $varname, $type, $default);
    }

    public function getPath()
    {
        return $this->path;
    }
}
