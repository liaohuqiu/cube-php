<?php
/**
 * 检测和规范输入数据接口
 *
 * 类型参数说明：
 *	'noclean'  - 不做处理
 *	'int'      - 转换成integer
 *	'unit'     - 转换成无符号integer
 *  'num'      - 转换成number
 *	'str'      - 转换成string，并去除两边的空格
 *	'notrim'   - 转换成string，保留空格
 *	'file'     - 转换成file，不支持数组提交
 *	'json'     - JSON
 *
 * @author http://www.liaohuqiu.net
 */
class MCore_Tool_Input
{
    private static $globalSources = array (
        'g' => '_GET',
        'p' => '_POST',
        'c' => '_COOKIE',
        'r' => '_REQUEST',
        'f' => '_FILES'
    );

    /**
     * 获取输入
     */
    public static function clean($source, $varname, $type = 'noclean', $default = null)
    {
        self::processMagicQuotes();

        $container = $GLOBALS[self::$globalSources[$source]];
        if (!isset($container[$varname]))
        {
            if ($default != null)
            {
                return $default;
            }
            $var = '';
        }
        else
        {
            $var = $container[$varname];
        }

        return self::cast($var, $type);
    }

    private static function processMagicQuotes()
    {
        static $hasProcessed = false;

        if (!$hasProcessed && get_magic_quotes_gpc())
        {
            $_GET = self::stripslashesDeep($_GET);
            $_POST = self::stripslashesDeep($_POST);
            $_COOKIE = self::stripslashesDeep($_COOKIE);
            $_REQUEST = self::stripslashesDeep($_REQUEST);
            $hasProcessed = true;
        }
    }

    private static function &cast($data, $type)
    {
        switch ($type)
        {
        case 'noclean':
            break;
        case 'int':
            $data = intval($data);
            break;
        case 'uint':
            $data = max(0, intval($data));
            break;
        case 'num':
            $data = $data + 0;
            break;
        case 'str':
            $data = trim(self::getStr($data));
            break;
        case 'notrim':
            $data = self::getStr($data);
            break;
        case 'file':
            if (!is_array($data))
            {
                $data = array(
                    'name'     => '',
                    'type'     => '',
                    'size'     => 0,
                    'tmp_name' => '',
                    'error'    => UPLOAD_ERR_NO_FILE,
                );
            }
            break;
        case 'json':
            $data = trim(self::getStr($data));
            if($data)
            {
                $data = json_decode($data,true);
            }
            else
            {
                $data = array();
            }
            break;
        default:
            throw new Exception('Unsupport type');
        }
        return $data;
    }

    private static function getStr($data)
    {
        return $data;
    }

    /**
     * 递归 stripslashes
     */
    private static function stripslashesDeep($value)
    {
        if (is_array($value))
        {
            foreach ($value as $sKey => $vVal)
            {
                $value[$sKey] = self::stripslashesDeep($vVal);
            }
        }
        else if (is_string($value))
        {
            return stripslashes($value);
        }
        return $value;
    }
}
