<?php 
/**
 *  ip
 *  
 * @author      huqiu
 */
class MCore_Tool_Ip 
{
    public static function isInnerIP($sIp)
    {   
        if ('127.0.0.1' === $sIp)
        {
            return true;
        }
        list($i1, $i2, $i3, $i4) = explode('.', $sIp, 4);
        return ($i1 == 10 || ($i1 == 172 && 16 <= $i2 && $i2 < 32) || ($i1 == 192 && $i2 == 168));
    }

    public static function getOuterIP($sIp)
    {
        $ips = preg_split('/;|,|\s/', $sIp);
        $sIp = 'unknown';
        foreach ($ips as $ip)
        {
            $ip = trim($ip);
            if (false === ip2long($ip))
            {
                continue;
            }
            $sIp = $ip;
            if (!self::isInnerIP($ip))
            {
                break;
            }
        }
        return $sIp;
    }

    public static function getClientIP()
    {
        $fip = getenv('HTTP_X_FORWARDED_FOR').' '.getenv('HTTP_VIA').' '.getenv('REMOTE_ADDR');
        return self::getOuterIP($fip);
    }

    public static function getServerIp()
    {
        $sIp = getenv('SERVER_ADDR');
        if ($sIp == '' || $sIp == '127.0.0.1')
        {
            $sIp = 'unknown';
        }
        return $sIp;
    }

    function getInnerIP()
    {
        $devices = exec("/sbin/ip addr|grep '^[0-9]'|awk '{print $2}'|sed s/://g|tr '\n' ' '");
        $device = explode(' ', $devices);
        foreach($device as $dev)
        {
            if ($dev == lo) { continue; }
            $ip = self::getLocalIp($dev);
            if (self::isInnerIP($ip))
            {
                    return $ip;
            }
        }
    }

    function getLocalIp($interface = "eth0")
    {
        $str = exec("/sbin/ifconfig ".$interface." | grep 'inet addr'");
        $str = explode(":", $str, 2);
        $str = explode(" ", $str[1], 2);
        return $str[0];
    }
}
?>