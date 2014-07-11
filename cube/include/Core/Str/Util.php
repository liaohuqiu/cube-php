<?php
/**
 *   字符串操作
 *
 * @author      huqiu
 */
class MCore_Str_Util
{
    /**
     * 两个字符串之间
     */
    public function between($delimiter1, $delimiter2, $subject)
    {
        $arr = explode($delimiter2, $subject);
        if (!$arr || empty($arr) || count($arr) == 1)
        {
            return '';
        }
        $oldStr = trim(array_shift($arr));
        $arr = explode($delimiter1, $oldStr);
        if (!$arr || empty($arr))
        {
            return '';
        }
        return trim(array_pop($arr));
    }

    /**
     * $ptn {key} 对应data[$key]
     */
    public static function displayPtn($data, $ptn)
    {
        if (!$data)
        {
            return $ptn;
        }

        $trans = array();
        foreach ($data as $key=>$value)
        {
            $key = "{".$key."}";
            $trans[$key] = $value;
        }
        $word = strtr($ptn, $trans);
        return $word;
    }

    public static function shorten($str, $len, $fn_len = 'strlen', $fn_sub = 'substr')
    {
        if ($fn_len($str) > $len)
        {
            return $fn_sub($str, 0, $len - 1) . "...";
        }
        return $str;
    }

    public static function startWith($str, $tagStr)
    {
        return substr($str,0,strlen($tagStr)) === $tagStr;
    }

    public static function endWith($str, $tagStr)
    {
        $len = strlen($tagStr);
        return substr($str, -$len, $len) === $tagStr;
    }
}
