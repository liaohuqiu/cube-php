<?php
class MCore_Proxy_Servant
{
    public static function cmd($cmd, $data)
    {
        $postData = array();
        $postData['cmd'] = $cmd;
        $postData['data'] = json_encode($data);
        foreach (MCore_Tool_Conf::getDataConfigByEnv('mix', 'cube-servant-list') as $url)
        {
            $data = MCore_Tool_Http::post($url, $postData);
            if ($data != 'ok')
            {
                throw new Exception('Error on call cube servant: ' . $url . ' , ' . $data);
            }
        }
        return true;
    }

    public static function updateDataConfig($key, $data)
    {
        $postData = array();
        $postData['key'] = $key;
        $postData['data'] = $data;
        return self::cmd('update-config', $postData);
    }
}
