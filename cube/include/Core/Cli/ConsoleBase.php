<?php
/**
 *
 * @author huqiu
 */
if (!function_exists('p'))
{
    function pf()
    {
        $argv = func_get_args();
        $format = array_shift($argv);
        $msg = vsprintf($format, $argv);
        p($msg);
    }

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
    private $lockfp;

    protected function getInputOption($required, $optional = array())
    {
        $opt = new MCore_Cli_Options($required, $optional);
        return $opt;
    }

    protected function execCmd($cmd)
    {
        $this->printInfo($cmd, false);
        exec($cmd, $result);
        return $result;
    }

    protected function checkLock()
    {
        $fileName = basename($this->argv[0]) . '.lock';
        $lock = MCore_Tool_Lock::getLock($fileName);
        if (!$lock)
        {
            p('check lock fail');
            exit;
        }
        $this->lockfp = $lock;
    }

    protected function printInfo($msg, $withTime = true)
    {
        return p($msg, $withTime);
    }

    protected function setNoLimit()
    {
        ini_set("memory_limit", -1);
        return $this;
    }

    public function run()
    {
        try
        {
            $this->argc = $_SERVER['argc'];
            $this->argv = $_SERVER['argv'];

            $this->init();

            $this->main();

            $this->ouput();
        }
        catch (Exception $ex)
        {
            $msg = $ex->getMessage() . "\n";
            $msg .= $ex->getTraceAsString();
            p($msg);
        }
    }

    protected function init()
    {
    }

    protected function main()
    {
    }

    protected function ouput()
    {
    }
}
