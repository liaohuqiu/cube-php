<?php
/**
 * a simple log tool
 *
 * @author huqiu
 */
if (!is_writable(WRITABLE_DIR))
{
    throw new Exception('WRITABLE_DIR is not writable: ' . WRITABLE_DIR);
}
class MCore_Tool_Log
{
    /**
     * level : dev / test /prod
     * addDebugLog($msg);
     * addDebugLog($logname, $msg, $level, $ip)
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

        if (MCore_Tool_Env::isProd() && $level != 'prod')
        {
            return;
        }
        if ($ip && self::getIp() != $ip)
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

    private static function wantFile($filename)
    {
        static $fps;
        $dir = WRITABLE_DIR . '/log';
        if (!$fps)
        {
            $fps = array();
            if (!is_dir($dir))
            {
                mkdir($dir, 0777, true);
            }
        }
        if (!isset($fps[$filename]))
        {
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
            $fps[$filename] = fopen($fn, 'a');
        }
        return $fps[$filename];
    }

    public static function addFileLog($filename, $desc, $micro=false)
    {
        $fp = self::wantFile($filename);
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

            $filesize = filesize($fn);
            if ($filesize >= 1024 * 1024 * 1024)
            {
                $newfilename = $fn.'.'.date('YmdHis');
                rename($fn, $newfilename);
            }
        }
    }

    public static function addSysLog($ident, $message, $priority = LOG_NOTICE)
    {
        if(!is_string($message))
        {
            $message = var_export($message,true);
        }
        $message = strtr($message, array("\n" => '\n'));
        openlog($ident, LOG_PID, LOG_LOCAL6);
        syslog($priority, $message);
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
