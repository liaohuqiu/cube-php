<?php
/**
 *   http
 *
 * @author      huqiu
 */
class MCore_Tool_Http
{
    public static function buildGetUrl($info,$url = "")
    {
        $list = array();
        foreach($info as $key=>$val)
        {
            $list[] ="$key=$val";
        }
        $link = "$url?".implode("&",$list);
        return $link;
    }

    public static function parse_str($str)
    {
        $op = array();
        $pairs = explode("&", $str);
        foreach ($pairs as $pair)
        {
            if($pair)
            {
                list($k, $v) = array_map("urldecode", explode("=", $pair));
                $op[$k] = $v;
            }
        }
        return $op;
    }

    public static function get($url, $param=array(), $headers=array(),$cookieArr = array(), $referer='', $agent='')
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

        $curl=curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION,true);
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, false);

        if(!empty($cookieArr))
        {
            self::buildCookie($curl,$cookieArr);
        }
        $content = curl_exec($curl);
        curl_close($curl);

        return $content;
    }

    /**
     * 设置请求的cookie头信息
     */
    private static function buildCookie($curl,$cookieArr)
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
        curl_setopt($curl, CURLOPT_COOKIE, $cookieStr);
        return true;
    }

    public static function post($url, $param, $referer='', $agent='Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)', $headers=array(), $timeOut = 5)
    {
        if (empty($param))
        {
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); //
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeOut);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_USERAGENT, $agent);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION,true);
        curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if(is_array($param))
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));
        }
        else
        {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
        }

        $content = curl_exec($curl);
        curl_close($curl);

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

    /**
     * 用于腾讯微博
     */
    public static function http( $url , $params , $method='GET' , $multi = array() , $extheaders = array())
    {
        $method = strtoupper($method);
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_USERAGENT, 'PHP-SDK OAuth2.0');
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ci, CURLOPT_TIMEOUT, 3);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ci, CURLOPT_HEADER, false);
        $headers = (array)$extheaders;
        switch ($method)
        {
        case 'POST':
            curl_setopt($ci, CURLOPT_POST, TRUE);
            if (!empty($params))
            {
                if($multi)
                {
                    foreach($multi as $key => $file)
                    {
                        $params[$key] = '@' . $file;
                    }
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $params);
                    $headers[] = 'Expect: ';
                }
                else
                {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($params));
                }
            }
            break;
        case 'DELETE':
        case 'GET':
            $method == 'DELETE' && curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
            if (!empty($params))
            {
                $url = $url . (strpos($url, '?') ? '&' : '?')
                    . (is_array($params) ? http_build_query($params) : $params);
            }
            break;
        }
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );
        curl_setopt($ci, CURLOPT_URL, $url);
        if($headers)
        {
            curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
        }

        $response = curl_exec($ci);
        curl_close ($ci);
        return $response;
    }
}

?>
