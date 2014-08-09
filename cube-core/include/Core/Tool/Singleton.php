<?php
/**
 *
 * @package     core
 * @subpackage  tool
 * @author      huqiu
 */
class MCore_Tool_Singleton
{
    private static $instanceList = array();

    public static function getInstance($className)
    {
        $key = strtolower($className);
        if(!isset(self::$instanceList[$key]))
        {
            $instance = self::createInstance($className);
            self::$instanceList[$key] = $instance;
        }
        return self::$instanceList[$key];
    }

    private static function createInstance($name)
    {
        $name[1] = strtoupper($name[1]);
        $arr = explode('_',$name);
        $arr = array_map('ucfirst',$arr);
        $className = implode('_',$arr);
        if (class_exists($className))
        {
            return  new $className();
        }
        else
        {
            throw new Exception('Can not create instance for this class, class is no exsitent: ' . $className);
        }
    }
}
?>
