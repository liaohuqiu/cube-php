<?php
/**
 *	字符串检查
 *
 * @author
 */
class MCore_Str_Check
{
    public static function checkEmail($email)
    {
        //检查email用户和域名字符串长度，rfc规定最大不超过320,本应用限定128个字符
        if (!ereg("[^@]{1,64}@[^@]{1,255}", $email) || strlen($email) > 128)
        {
            return false;
        }

        //分割email地址，分隔符: '@'
        $email_array = explode("@", $email);
        if(count($email_array) != 2)
        {
            return false;
        }

        //检查Eamil user部分，即'@'前面部分的字符串
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < count($local_array); $i++)
        {
            if(!ereg("^(([a-za-z0-9!#$%&*+/=?^_`{|}~-][a-za-z0-9!#$%&*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
            {
                return false;
            }
        }
        ////检查Eamil 域名部分，即'@'后面部分的字符串，域名或IP地址
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
        {
            $domain_array = explode(".", $email_array[1]);
            if (count($domain_array) < 2)
            {
                return false; //域名格式不正确
            }
            for ($i = 0; $i < count($domain_array); $i++)
            {
                if (!ereg("^(([a-za-z0-9][a-za-z0-9-]{0,61}[a-za-z0-9])|([a-za-z0-9]+))$", $domain_array[$i]))
                {
                    return false;
                }
            }
        }
        return true;
    }

    public static function checkMobile($mobile)
    {
        if(strlen($mobile) == 11 && preg_match("/13\d{9}|15[1235689]\d{8}|18\d{9}/", $mobile))
        {
            return true;
        }
        return false;
    }

    // 把字符串中的非UTF-8字符转换成U+FFFD
    // php的json_encode只支持UTF-8数据 若有非UTF-8数据 则直接返回null
    // 因此需要保证入库的数据必须是合法的utf-8数据
    public static function sanitizeUTF8($string)
    {
        if (self::isUTF8($string))
        {
            return $string;
        }

        $result = array();

        $regex =
            "/([\x01-\x7F]".
            "|[\xC2-\xDF][\x80-\xBF]".
            "|[\xE0-\xEF][\x80-\xBF][\x80-\xBF]".
            "|[\xF0-\xF4][\x80-\xBF][\x80-\xBF][\x80-\xBF])".
            "|(.)/";

        $offset = 0;
        $matches = null;
        while (preg_match($regex, $string, $matches, 0, $offset))
        {
            if (!isset($matches[2])) {
                $result[] = $matches[1];
            }
            else
            {
                // U+FFFD.
                $result[] = "\xEF\xBF\xBD";
            }
            $offset += strlen($matches[0]);
        }

        return implode('', $result);
    }

    public static function isUTF8($string)
    {
        return mb_check_encoding($string, 'UTF-8');
    }
}
