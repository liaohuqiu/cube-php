<?php
/**
 * 获取翻页块html的类
 *
 * @author
 */
class MCore_Str_Html
{
    public static function options($nameValueList, $currentValue = null)
    {
        $html = '';
		foreach($nameValueList as $name=>$value)
        {
            if($value == $currentValue)
            {
                $html .= "<option value='$value' selected = 'true'>$name</option>";
            }
            else
            {
                $html .= "<option value='$value'>$name</option>";
            }
        }
        return $html;
    }

    public function getPagehtml($start, $num, $total, $app, $showFirstLast = false)
    {
        $pagehtml = "";

        $app = strpos($app, '?') ? $app."&start=" : $app."?start=";
        $pagenum = 5;
        $curpage = floor( $start / $num ) + 1;
        $totalpage = ceil( $total / $num );
        $minpage = max( $curpage - round( $pagenum / 2 ) + 1, 1 );
        $maxpage = min( $minpage + $pagenum - 1, $totalpage );
        $minpage = max($maxpage - $pagenum + 1, 1);

        if ( $totalpage <= 1 )
        {
            return "";
        }

        if ( $curpage > 1 )
        {
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a href=\"" . $app . "0\" onfocus=\"this.blur();\" class=\"word\"><span>首页</span></a>";
            }
            $pagehtml .= "<a href=\"" . $app . ( ( $curpage-2 ) * $num ) . "\" onfocus=\"this.blur();\" class=\"word\"><span>上一页</span></a>";
        }

        for ( $i = $minpage; $i <= $maxpage; $i++ )
        {
            if ( $i != $curpage )
            {
                $pagehtml .= "<a href=\"" . $app . ( ( $i-1 ) * $num ) . "\" onfocus=\"this.blur();\"><span>" . $i . "</span></a>";
            }
            else
            {
                $pagehtml .= "<a href=\"#\" class=\"on\"><span>" . $i . "</span></a>";
            }
        }

        if ( $curpage < $totalpage )
        {
            $pagehtml .= "<a href=\"" . $app . ( $curpage * $num ) . "\" onfocus=\"this.blur();\" class=\"word\"><span>下一页</span></a>";
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a href=\"" . $app . ( ( $totalpage-1 ) * $num ) . "\" onfocus=\"this.blur();\" class=\"word\"><span>末页</span></a>";
            }
        }
        return '<div class="ui_page">' . $pagehtml . '</div>';
    }

    /*

    */
    public function getPagehtmlByList($listData,$app,$showFirstLast = false)
    {
        $start = intval($listData['start']);
        $num = intval($listData['num']);
        $total = intval($listData['total']);
        return self::getPagehtml( $start, $num, $total, $app,$showFirstLast);
    }

    function getJsPagehtml( $start, $num, $total,$showFirstLast = false)
    {
        $pagenum = 5;
        $curpage = floor( $start / $num ) + 1;
        $totalpage = ceil( $total / $num );
        $minpage = max( $curpage - round( $pagenum / 2 ) + 1, 1 );
        $maxpage = min( $minpage + $pagenum - 1, $totalpage );
        $minpage = max($maxpage - $pagenum + 1, 1);

        if ( $totalpage <= 1 )
        {
            return "";
        }

        $pagehtml = "";
        if ( $curpage > 1 )
        {
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a href=### class=\"_j_page\" data-start=0 onfocus=\"this.blur();\" class=\"word\"><span>首页</span></a> ";
            }
            $pagehtml .= "<a href=### class=\"_j_page\" data-start=".( ( $curpage-2 ) * $num )." onfocus=\"this.blur();\" class=\"word\"><span>上一页</span></a>";
        }

        for ( $i = $minpage; $i <= $maxpage; $i++ )
        {
            if ( $i != $curpage )
            {
                $pagehtml .= "<a href=### class=\"_j_page\" data-start=".( ( $i-1 ) * $num )." onfocus=\"this.blur();\"><span>" . $i . "</span></a>";
            }
            else
            {
                $pagehtml .= "<a href=\"#\" class=\"on\"><span>" . $i . "</span></a>";
            }
        }

        if ( $curpage < $totalpage )
        {
            $pagehtml .= "<a href=### class=\"_j_page\" data-start=".( $curpage * $num )." onfocus=\"this.blur();\" class=\"word\"><span>下一页</span></a>";
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a href=\### class=\"_j_page\" data-start=".( ( $totalpage-1 ) * $num )." onfocus=\"this.blur();\" class=\"word\"><span>末页</span></a>";
            }
        }
        return $pagehtml;
    }

    public function ajaxPagehtml( $start, $num, $total, $app, $showFirstLast = false)
    {
        $pagehtml = "";

        $app = strpos($app, '?') !== false ? $app."&_ajax=1&start=" : $app."?_ajax=1&start=";
        $pagenum = 5;
        $curpage = floor( $start / $num ) + 1;
        $totalpage = ceil( $total / $num );
        $minpage = max( $curpage - round( $pagenum / 2 ) + 1, 1 );
        $maxpage = min( $minpage + $pagenum - 1, $totalpage );
        $minpage = max($maxpage - $pagenum + 1, 1);

        if ( $totalpage <= 1 )
        {
            //return "共" . $total . "条";
            return "";
        }

        if ( $curpage > 1 )
        {
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a class=\"word\" data-ajax=\"request\" href=\"" . $app . "0\" onfocus=\"this.blur();\"><span>首页</span></a>";
            }
            $pagehtml .= "<a class=\"word\" data-ajax=\"request\" href=\"" . $app . ( ( $curpage-2 ) * $num ) . "\" onfocus=\"this.blur();\"><span>上一页</span></a>";
        }

        for ( $i = $minpage; $i <= $maxpage; $i++ )
        {
            if ( $i != $curpage )
            {
                $pagehtml .= "<a data-ajax=\"request\" href=\"" . $app . ( ( $i-1 ) * $num ) . "\" onfocus=\"this.blur();\"><span>" . $i . "</span></a>";
            }
            else
            {
                $pagehtml .= "<a class=\"on\"><span>" . $i . "</span></a>";
            }
        }

        if ( $curpage < $totalpage )
        {
            $pagehtml .= "<a class=\"word\" data-ajax=\"request\" href=\"" . $app . ( $curpage * $num ) . "\" onfocus=\"this.blur();\"><span>下一页</span></a>";
            if ($totalpage > 5 && $showFirstLast)
            {
                $pagehtml .= "<a class=\"word\" data-ajax=\"request\" href=\"" . $app . ( ( $totalpage-1 ) * $num ) . "\" onfocus=\"this.blur();\"><span>末页</span></a>";
            }
        }
        // TODO style...
        return '<div class="ui_page tac" style="margin-top: 30px;"><div class="ui_page">' . $pagehtml . '</div></div>';
    }

    static function addLink($src, $withimg)
    {
        if (0 == preg_match("/mms:\/\/|http:\/\/|ftp:\/\/|https:\/\/|www\./i", $src, $res, PREG_OFFSET_CAPTURE))
        {
            return self::space2nbsp($src);
        }

        $len = strlen($src);
        $start = $res[0][1];
        for ($end=$start; $end<$len; $end++)
        {
            $vchr = $src[$end];
            $ov = ord($vchr);

            if($end+6<$len)
            {
                $fourchar = substr($src,$end,4);
                $sixchar = substr($src,$end,6);
                if($fourchar == "&lt;" || $fourchar == "&gt;")
                {
                    break;
                }
                if($sixchar == "&quot;")
                {
                    break;
                }
            }
            else if($end+4<$len)
            {
                $fourchar = substr($src,$end,4);
                if($fourchar == "&lt;" || $fourchar == "&gt;")
                {
                    break;
                }
            }

            if ($ov <= 32
                || $vchr == "'"
                || $vchr == '"'
                || $vchr == '<'
                || $vchr == '>'
                || $ov >= 128)
            {
                break;
            }
        }

        $url = substr($src, $start, $end - $start);
        $posgt = strpos($src, ">", $end);
        $poslt = strpos($src, "<", $end);
        if (($posgt !== false && $poslt !== false && $poslt > $posgt)
            || ($posgt !== false && $poslt === false))
        {
            return self::space2nbsp(substr($src, 0, $start)).$url.self::addLink(substr($src, $end), $withimg);
        }
        else if ($withimg && self::IsImage($url))
        {
            return self::space2nbsp(substr($src, 0, $start))."<img src=\"".(strtolower(substr($url , 0 , 4)) == "www."?"http://".$url:$url)."\" border=0>".self::addLink(substr($src, $end), $withimg);
        }

        // 去除url尾部的空格
        $nbsp = "";
        while (substr($url, -6, 6) == "&nbsp;")
        {
            $nbsp .= "&nbsp;";
            $url = substr($url, 0, strlen($url)-6);
        }
        return self::space2nbsp(substr($src, 0, $start))."<a href=\"".(strtolower(substr($url , 0 , 4)) == "www."?"http://".$url:$url)."\" target=_blank onclick=\"event.cancelBubble=true;\" onmousedown=\"javascript:event.cancelBubble=true;\">".$url."</a>".self::addLink($nbsp.substr($src, $end), $withimg);
    }

    static function space2nbsp($str)
    {
        return str_replace("\n ", "\n&nbsp;", str_replace("  ", "&nbsp; ", $str));
    }

    function IsImage($url)
    {
        $imgfile = array(".gif", ".png", ".x-png", ".jpg", ".jpeg", "pjpeg");
        foreach($imgfile as $tmp)
        {
            $len = strlen($tmp);
            if (0 == strncasecmp($tmp, substr($url, strlen($url)-$len), $len))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * {{avatar class="ui_blk_left" href="/{{$userinfo.tinyurl}}" usercard=1 width="50" src=$userinfo.logo50 data_uid="" data_xxx="def"}}
     *
     */
    public static function getAvatarHtml($params)
    {
        $href = htmlspecialchars($params["href"]);
        $class = htmlspecialchars($params["class"]);
        $usercard = intval($params["usercard"]) > 0 ? true : false;
        $width = intval($params["width"]);
        $src = htmlspecialchars($params["src"]);
        $target = htmlspecialchars($params["target"]);

        $targetHtml = "";
        if (strlen($target))
        {
            $targetHtml = ' target="'.htmlspecialchars($target).'"';
        }

        $innerClass = "";
        if ($usercard)
        {
            $innerClass = " _j_g_ucard";
        }

        $attrHtml = '';
        foreach ($params as $key => $value)
        {
            if (strpos($key, "data_") === 0)
            {
                $key = str_replace('_', '-', $key);
                $attrHtml .= ' '.htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
            }
        }

        $html = '<span class="avatar size'.$width.$innerClass.'" style="background-image:url('.$src.');width:'.$width.'px;height:'.$width.'px;"'.$attrHtml.'></span>';

        // 如果$href为空 则输出不带链接的
        if (strlen($href))
        {
            $html = '<a class="ui_avatar '.$class.'" href="'.$href.'"'.$targetHtml.'>'.$html.'</a>';
        }
        else if (strlen($class))
        {
            $html = '<span class="ui_avatar '.$class.'" href="'.$href.'"'.$targetHtml.'>'.$html.'</span>';
        }
        return $html;
    }
}
?>
