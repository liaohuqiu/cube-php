<?php
/**
 * a simple log tool
 *
 * @author huqiu
 */
class MCore_Tool_Log
{
    public static function logReady()
    {
        return defined('LOG_DIR');
    }

    public static function getLogDir()
    {
        return LOG_DIR;
    }

    /**
     * level : dev / test /prod
     */
    public static function addDebugLog()
    {
        $argv = func_get_args();
        $argc = func_num_args();
        if ($argc == 1)
        {
            $v = $argv[0];
            $logName = APP_NAME;
        }
        else
        {
            $logName = $argv[0];
            $v = $argv[1];
        }

        $level = 'dev' && isset($argv[2]) && $level = $argv[2];
        $ip = '' && isset($argv[3]) && $ip = $argv[3];

        if(MCore_Tool_Env::isProd() && $level != 'prod')
        {
            return;
        }
        if($ip && self::getIp() != $ip)
        {
            return;
        }
        $desc = $v;
        if (!is_string($v))
        {
            try
            {
                $desc = var_export($v,true);
            }
            catch(Exception $ex)
            {
                ob_start();
                var_dump($v);
                $desc = ob_get_clean();
            }
        }
        self::addFileLog('debug_'.$logName, $desc);
    }

    public static function addFileLog($filename, $desc, $micro=false)
    {
        if (!defined('LOG_DIR'))
        {
            throw new Exception('LOG_DIR undifiend');
        }
        $dir = LOG_DIR;
        if (!is_dir(LOG_DIR))
        {
            mkdir($dir, 0777, true);
        }
        $fn = $dir . DS . $filename;
        if (!file_exists($fn))
        {
            touch($fn);
            chmod($fn, 0666);
        }
        if (!is_writable($fn))
        {
            return;
        }
        $fp = fopen($fn, 'a');
        if ($fp)
        {
            $time = date('Y-m-d H:i:s');
            if ($micro)
            {
                $time .= "\t" . self::getMicro();
            }
            if(is_array($desc) || is_object($desc))
            {
                $desc = var_export($desc,true);
            }
            $flog = sprintf("%s\t%s\t%s\t%s\n",$time, self::getIp(), self::getPid(), $desc);
            fwrite($fp, $flog);
            fclose($fp);

            $filesize = filesize($fn);
            if ($filesize >= 1024 * 1024 * 1024)
            {
                $newfilename = $fn.'.'.date('YmdHis');
                rename($fn, $newfilename);
            }
        }
    }

    function addSysLog($tag, $message,$app = APP_NAME, $priority = LOG_NOTICE)
    {
        if(!is_string($message))
        {
            $message = var_export($message,true);
        }
        openlog(strtoupper($app), LOG_PID, LOG_LOCAL6);
        syslog($priority, $tag . "\t" . $message);
        closelog();
    }

    private static function getPid()
    {
        static $pid = NULL;
        if (NULL === $pid)
        {
            $pid = getmypid().'.'.rand(0, 100000);
        }
        return $pid;
    }

    private static function getIp()
    {
        static $ip = NULL;
        if (NULL === $ip)
        {
            $ip = MCore_Tool_Ip::getClientIP();
        }
        return $ip;
    }

    private static function getMicro()
    {
        list($msec, $sec) = explode(" ", microtime());
        return ((float)$msec + (float)$sec);
    }
}
