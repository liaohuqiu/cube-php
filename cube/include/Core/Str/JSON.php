<?php
/**
 * JSON
 *
 * @author huqiu
 *
 */
class MCore_Str_JSON
{
    private static $_strMap = array(
        '<' => '\u003c',
        '>' => '\u003e',
        '&' => '\u0026',
        '\u2028' => '\\\u2028',
        '\u2029' => '\\\u2029',
    );

    /**
     * 安全编码Json数据
     */
    public static function stringify($data)
    {
        $json = json_encode($data);
        $json = strtr($json, self::$_strMap);
        return $json;
    }
}
?>
