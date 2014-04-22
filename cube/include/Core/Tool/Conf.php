<?php
/**
 *
 * @author      huqiu
 */
class MCore_Tool_Conf
{
    private static $_cache = array();
    private static $_includeConfFileCache = array();

    public static function getDataConfigPath($key)
    {
        $key = self::_ensureKeyFormat($key);
        return CONFIG_DATA_DIR . '/' . $key . '.php';
    }

    private static function _ensureKeyFormat($key)
    {
        strpos($key, '/') === 0 && $key = substr($key, 1);
        return $key;
    }

    public static function getDataConfigByEnv($key, $throwException = true)
    {
        $argv = func_get_args();
        $argv[0] = $key . '.' . ENV_TAG;
        $argc = func_num_args();
        return self::_callGetDataConfig($argv, $argc);
    }

    /**
     *  ($key, ..., $throwException);
     *  first key is the file base name in data-config directory.
     *  the other keys are the key of the config in nested array.
     */
    public static function getDataConfig()
    {
        $argv = func_get_args();
        $argc = func_num_args();
        return self::_callGetDataConfig($argv, $argc);
    }

    private static function _callGetDataConfig($argv, $argc)
    {
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

    public static function updateDataConfigByEnv($key, $data)
    {
        $key = $key . '.' . ENV_TAG;
        return self::updateDataConfig($key, $data);
    }

    public static function updateDataConfig($key, $data)
    {
        return MCore_Proxy_Servant::updateDataConfig($key, $data);
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

        $output = "<?php\n\$data = " . var_export($data, true) . ";\nreturn \$data;\n";
        file_put_contents($tempFilePath, $output);
        rename($tempFilePath, $filePath);
    }
}
