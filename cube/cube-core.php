<?php
/**
 *
 * @author      http://www.liaohuqiu.net
 */
class Cube
{
    private static $is_loaded = false;
    private static $include_dirs = array();
    private static $class_map = array();

    /**
     * Add include path
     */
    public static function addIncludePath($path)
    {
        self::$include_dirs = array_merge(self::$include_dirs, (array) $path);
    }

    public static function addClassMap($map)
    {
        self::$class_map = array_merge(self::$class_map, $map);
    }

    public static function boot()
    {
        if (self::$is_loaded)
        {
            return;
        }
        self::$is_loaded = true;
        self::initPara();
        self::register();
        self::$include_dirs[] = CUBE_ROOT_DIR . '/include';
    }

    public static function initPara()
    {
        if (!defined('CUBE_ROOT_DIR'))
        {
            throw new Exception('constant CUBE_ROOT_DIR undefined');
        }
        if (!defined('ENV_TAG'))
        {
            throw new Exception('constant ENV_TAG! undefined, should be dev / test / prod');
        }
        if (!defined('APP_NAME'))
        {
            throw new Exception('APP_NAME undefined');
        }
        if (empty(self::$include_dirs))
        {
            throw new Exception('include path has not been specified, call addIncludePath() fisrt.');
        }
        if (!defined('CONFIG_DATA_DIR'))
        {
            throw new Exception('CONFIG_DATA_DIR undefined');
        }
        if (!defined('WRITABLE_DIR'))
        {
            throw new Exception('WRITABLE_DIR undefined');
        }

        define('DS', '/');
        define('HYPHEN', '-');
        define('UNDERSCORE', '_');
        define('SYS_CODE', 'SRAIN');

        if (!defined('MCACHE_KEY_PRE'))
        {
            define('MCACHE_KEY_PRE', APP_NAME . '_');
        }
    }

    public static function autoload($class_name)
    {
        $class_name = ltrim($class_name, '\\');

        $file_name  = '';
        $namespace = '';

        if ($last_ns_pos = strrpos($class_name, '\\'))
        {
            $namespace = substr($class_name, 0, $last_ns_pos);
            $class_name = substr($class_name, $last_ns_pos + 1);
            $file_name  = str_replace('\\', DS, $namespace) . DS;
        }
        if (substr($class_name, 0,  1) === 'M')
        {
            $class_name = substr($class_name, 1);
        }

        $file_name .= str_replace('_', DS, $class_name) . '.php';

        foreach (self::$include_dirs as $include_path)
        {
            $class_file = $include_path . DS . $file_name;
            if (file_exists($class_file))
            {
                require_once($class_file);
                return true;
            }
        }
        return false;
    }

    public static function register()
    {
        spl_autoload_register(array('Cube', 'autoload'));
    }
}

// open this if you want
// register_shutdown_function( "fatal_handler" );
function fatal_handler()
{
    $error = error_get_last();
    if ($error)
    {
        echo '<pre/>';
        echo $error['message'];
    }
}
function add_debug_log($v)
{
    MCore_Tool_Log::addDebugLog($v);
}
function add_error_log($msg)
{
    MCore_Tool_Log::addSysLog(APP_NAME . '-error', $msg);
}
