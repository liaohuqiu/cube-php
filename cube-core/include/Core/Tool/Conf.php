<?php
/**
 *
 * @author      huqiu
 */
class MCore_Tool_Conf
{
    private static $_cache = array();
    private static $_includeConfFileCache = array();

    public static function setDataConfig($key, $data, $env = false)
    {
        if ($env)
        {
            $key = $key . '.' . ENV_TAG;
        }
        self::$_includeConfFileCache[$key] = $data;
    }

    public static function getDataConfigPath($key)
    {
        $key = self::_ensureKeyFormat($key);
        return CONFIG_DATA_DIR . '/' . $key . '.php';
    }

    public static function getDataConfigPathByEnv($key)
    {
        return self::getDataConfigPath($key . '.' . ENV_TAG);
    }

    private static function _ensureKeyFormat($key)
    {
        strpos($key, '/') === 0 && $key = substr($key, 1);
        return $key;
    }

    /**
     *  flatten it first, for he parent function my call this method in this way:
     *
     *      function getSysConfig()
     *      {
     *          return MCore_Tool_Conf::getDataConfig(func_get_args());
     *      }
     *
     *      or
     *
     *      function getSysConfig()
     *      {
     *          return MCore_Tool_Conf::getDataConfig('sys-config', func_get_args());
     *      }
     */
    public static function getDataConfig()
    {
        $argv = MCore_Tool_Array::flatten(func_get_args());
        return self::_callGetDataConfig($argv);
    }

    public static function getDataConfigByEnv()
    {
        $argv = MCore_Tool_Array::flatten(func_get_args());
        $argv[0] .= '.' . ENV_TAG;
        return self::_callGetDataConfig($argv);
    }

    /**
     *  ($key, ..., $throwException);
     *
     *  first key is the file base name in data-config directory.
     *  the other keys are the key of the config in nested array.
     *
     */
    private static function _callGetDataConfig($argv)
    {
        $argc = count($argv);
        $throwException = true;
        if (is_bool($argv[$argc - 1]))
        {
            $throwException = array_pop($argv);
        }

        $key = self::_ensureKeyFormat(array_shift($argv));

        if (!isset(self::$_includeConfFileCache[$key]))
        {
            $filePath = self::getDataConfigPath($key);
            if (!file_exists($filePath))
            {
                if ($throwException)
                {
                    $msg = 'Can not find data config file: ' . $filePath;
                    throw new Exception($msg);
                }
                else
                {
                    return false;
                }
            }
            else
            {
                self::$_includeConfFileCache[$key] = include($filePath);
            }
        }
        if (empty($argv))
        {
            return self::$_includeConfFileCache[$key];
        }
        else
        {
            $data = self::$_includeConfFileCache[$key];
            foreach ($argv as $k)
            {
                if (!isset($data[$k]) && $throwException)
                {
                    $msg = 'Can not find config in: [ ' . $key . ' ] for : ' . implode($argv, ',');
                    throw new Exception($msg);
                }
                $data = $data[$k];
            }
            return $data;
        }
    }

    public static function writeDataConfig($key, $data)
    {
        $key = self::_ensureKeyFormat($key);

        // update process cache first
        self::$_includeConfFileCache[$key] = $data;

        $filePath = self::getDataConfigPath($key);

        $dir = dirname($filePath);
        if (!is_writable($dir))
        {
            throw new Exception('this directory is not writable: ' . $dir);
        }

        if (!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }

        $tempFilePath = $filePath . '.tmp';

        $output = self::formatConfigData($data);
        file_put_contents($tempFilePath, $output);
        rename($tempFilePath, $filePath);
    }

    public static function formatConfigData($data)
    {
        return "<?php\n\$data = " . var_export($data, true) . ";\nreturn \$data;\n";
    }
}
