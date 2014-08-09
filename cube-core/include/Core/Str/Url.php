<?php
class MCore_Str_Url
{
    public static function buildUrl($info, $url = '')
    {
        if (empty($info))
        {
            return $url;
        }
        if (strpos($url, '?') === false)
        {
            $link = "$url?" . http_build_query($info);
        }
        else
        {
            $link = "$url&" . http_build_query($info);
        }
        return $link;
    }
}
