<?php
/**
 *
 * @author huqiu
 */
if (!function_exists('p'))
{
    function p($info, $withTime = true)
    {
        if ($info instanceof MCore_Util_ArrayLike)
        {
            $info = (string)$info;
        }
        if (!is_string($info))
        {
            $info = var_export($info, true);
        }
        if ($withTime)
        {
            $now = time();
            $timeStr = date( 'Y-m-d H:i:s', $now + 10 );
            $msg = "[$timeStr] $info\r\n";
        }
        else
        {
            $msg = "$info\r\n";
        }
        if ($return)
        {
            return $msg;
        }
        echo $msg;
    }
}

class MCore_Cli_ConsoleBase
{
    protected $argc;
    protected $argv;
    private $_lockfp;

    function getInputOption($required, $optional = array())
    {
        $opt = new MCore_Cli_Options($required, $optional);
        return $opt;
    }

    function execCmd($cmd)
    {
        $this->printInfo($cmd, false);
        exec($cmd, $result);
        return $result;
    }

    protected function checkLock($fileName = "")
    {
        $this->_lockfp = MCore_Tool_Lock::lock($fileName);
    }

    protected function printInfo($msg, $withTime = true)
    {
        return p($msg, $withTime);
    }

    function setNoLimit()
    {
        ini_set("memory_limit", -1);
    }

    function run()
    {
        try
        {
            $this->argc = $_SERVER['argc'];
            $this->argv = $_SERVER['argv'];

            $this->getPara();

            $this->checkPara();

            $this->main();

            $this->ouput();
        }
        catch (Exception $ex)
        {
            $msg = $ex->getMessage() . "\n";
            $msg .= $ex->getTraceAsString();
            $this->printInfo($msg, false);
        }
    }

    protected function main()
    {
    }

    protected function getPara()
    {
    }

    protected function checkPara()
    {
    }

    protected function ouput()
    {
    }
}
