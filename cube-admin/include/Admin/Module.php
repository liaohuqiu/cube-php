<?php
/**
 *   ºóÌ¨Ä£¿é
 *
 * @author      huqiu
 */
class MAdmin_Module
{
    private $base_path = '';
    private $path;
    private $list;
    private $current;
    private $module_auth_keys;
    private $user_auth_keys;
    private $user;

    public function __construct($path, $user)
    {
        $this->user = $user;
        $this->user_auth_keys = $user['auth_keys'];
        $this->path = $this->tidyUrl($path);
        $base_path = MCore_Tool_Conf::getDataConfig('admin', 'base_path', false);
        if ($base_path)
        {
            $base_path = self::removeLastSlash($str);
            $this->base_path = $base_path;
        }
        $this->process();
    }

    private function combineUrl($url1,$url2)
    {
        $url = $url1 . DS . $url2;
        $url = $this->tidyUrl($url);
        return $url;
    }

    private function tidyUrl($url)
    {
        $url = str_replace('///', DS, $url);
        $url = str_replace('//', DS, $url);
        return $url;
    }

    private static function removeLastSlash($str)
    {
        //  if the last postion of base_path is '/', remove it
        if (strpos($str, DS) == strlen($str) - 1)
        {
            $str = substr($str, 0, -1);
        }
        return $str;
    }

    public function getCurrentModuleInfo()
    {
        $list = MCore_Tool_Array::where($this->list, array('is_current' => 1));
        return reset($list);
    }

    private function checkUserHasAuth($module)
    {
        if (!isset($module['auth_key']))
        {
            throw Exception('This module in config has no auth_key');
        }
        $auth_key = $module['auth_key'];
        $module['user_has_auth'] = in_array($auth_key, $this->user_auth_keys) ? 1 : 0;
        $this->module_auth_keys[] = $auth_key;
        return $module;
    }

    private function process()
    {
        $modules = MCore_Tool_Conf::getDataConfig('admin', 'module_list', true);

        foreach ($modules as $key => $module)
        {
            $root_path = $this->base_path . $module['root_path'];
            $module_index_url = $this->combineUrl($root_path , $module['url']);
            if (strpos($this->path, $module_index_url) !== false)
            {
                $module['is_current'] = 1;
            }
            $module['url'] = $module_index_url;
            !$module['name'] && $module['name'] = $key;
            !$module['des'] && $module['des'] = $key;

            $module = $this->checkUserHasAuth($module);

            foreach ($module['units'] as $index => $unit)
            {
                foreach ($unit['list'] as $idx => $item)
                {
                    $item_url = $this->combineUrl($root_path, $item['url']);
                    if (strpos($this->path, $item_url) !== false)
                    {
                        $item['is_current'] = 1;
                        $module['is_current'] = 1;
                    }
                    $item['url'] = $item_url;
                    $unit['list'][$idx] = $item;
                }
                $module['units'][$index] = $unit;
            }
            $modules[$key] = $module;
        }
        $this->list = $modules;
    }

    public function getModuleList()
    {
        if ($this->user->isSystemAdmin())
        {
            return $this->list;
        }
        $list = MCore_Tool_Array::where($this->list, array('user_has_auth' => 1));
        return $list;
    }

    public function getBasePath()
    {
        return $this->base_path;
    }

    public function userHasAuth()
    {
        if ($this->user->isSystemAdmin())
        {
            return true;
        }
        $current = $this->getCurrentModuleInfo();
        if ($current && !$current['user_has_auth'])
        {
            return false;
        }
        return true;
    }

    public function getModuleAuthKeys()
    {
        return $this->module_auth_keys;
    }
}
