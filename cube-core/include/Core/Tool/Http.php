<?php
/**
 *   http
 *
 * @author      huqiu
 */
class MCore_Tool_Http
{
    public static function getHost()
    {
        static $host;
        if (!$host)
        {
            $host = $_SERVER['HTTP_HOST'];
            if (($pos = strpos($host, ':')) !== false)
            {
                $host = substr($host, 0, $pos);
            }
        }
        return $host;
    }

    public static function buildGetUrl($info, $url = '')
    {
        if (empty($info))
        {
            return $url;
        }
        $link = $url . '?' . http_build_query($info);
        return $link;
    }

    public static function parse_str($str)
    {
        $op = array();
        $pairs = explode("&", $str);
        foreach ($pairs as $pair)
        {
            if ($pair)
            {
                list($k, $v) = array_map("urldecode", explode("=", $pair));
                $op[$k] = $v;
            }
        }
        return $op;
    }

    public static function get($url, $param = array(), $headers = array(),$cookieArr = array(), $referer = '', $agent = '')
    {
        if(!$agent)
        {
            $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)';
        }
        if (!empty($param))
        {
            $url .= strstr('?', $url) ? '&':'?';
            $url .= http_build_query($param);
        }

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION,true);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);

        if(!empty($cookieArr))
        {
            self::buildCookie($ch,$cookieArr);
        }
        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }

    private static function buildCookie($ch, $cookieArr)
    {
        if(empty($cookieArr))
        {
            return false;
        }
        $contentList = array();
        foreach($cookieArr as $key=>$value)
        {
            $contentList[] = "$key=$value";
        }
        $cookieStr = implode("; ",$contentList);
        curl_setopt($ch, CURLOPT_COOKIE, $cookieStr);
        return true;
    }

    public static function post($url, $param, $referer = '', $agent = '', $headers = array(), $timeOut = 5)
    {
        if (empty($param))
        {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIESESSION,true);
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if(is_array($param))
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
        }
        else
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }

        $content = curl_exec($ch);
        if(curl_errno($ch))
        {
            return curl_error($ch);
        }
        curl_close($ch);

        return $content;
    }

    public static function build_http_query_multi($params)
    {
        if (!$params) return '';

        uksort($params, 'strcmp');

        $pairs = array();

        $boundary = '';
        $boundary = uniqid('------------------');

        $MPboundary = '--'.$boundary;
        $endMPboundary = $MPboundary. '--';
        $multipartbody = '';

        foreach ($params as $parameter => $value)
        {
            if(in_array($parameter, array('pic', 'image', 'Filedata')) && $value{0} == '@' )
            {
                $url = ltrim( $value, '@' );
                $content = file_get_contents( $url );
                $array = explode( '?', basename( $url ) );
                $filename = $array[0];

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
                if($parameter == 'Filedata')
                {
                    $content_type = 'video/quicktime';
                }
                else
                {
                    $content_type = 'image/unknown';
                }
                $multipartbody .= "Content-Type: ". $content_type ."\r\n\r\n";
                $multipartbody .= $content. "\r\n";
            }
            else if($parameter == 'blob')
            {
                if(is_array($value))
                {
                    $name = $value["name"];
                    $filename = $value["filename"];
                    $content = $value["data"];
                    $content_type = $value['content_type'];
                }
                else
                {
                    $content = $value;
                }

                !$name && $name = 'pic';
                !$content_type && $content_type = 'image/unknown';
                !$filename && $filename = tempnam("tmp", "baobeipost_tmp");
                $filename = basename($filename);

                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $filename . '"'. "\r\n";
                $multipartbody .= "Content-Type: ".$content_type."\r\n\r\n";
                $multipartbody .= $content. "\r\n";
            }
            else
            {
                $multipartbody .= $MPboundary . "\r\n";
                $multipartbody .= 'Content-Disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
                $multipartbody .= $value."\r\n";
            }
        }

        $multipartbody .= $endMPboundary;
        return array($boundary, $multipartbody);
    }
}
