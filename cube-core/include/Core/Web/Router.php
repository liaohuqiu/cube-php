<?php
/**
 *  baisc:
 *
 *      /admin/server/info              => MApps_Admin_Server_Info
 *      /admin/server/info/             => MApps_Admin_Server_Info
 *      /admin/server/info.php          => MApps_Admin_Server_Info
 *      /admin/server/info.html         => MApps_Admin_Server_Info
 *      /admin/server/offline-list      => MApps_Admin_Server_OffLineList
 *
 *  permalink with input: using config array(/admin/server/info, array('server_id'));
 *
 *      /admin/server/info/341894.htm   => MApps_Admin_Server_Info
 *
 *  dialog / ajax:
 *
 *      /admin/server/info-ajax         => MApps_Admin_Server_InfoAjax
 *      /admin/server/info-dialog       => MApps_Admin_Server_InfoDialog
 *
 *  api:
 *
 *      /api/admin/server/info          => MApis_Admin_Server_Info
 *      /api/admin/server/info.json     => MApis_Admin_Server_Info
 *      /api/admin/server/info?v=1      => MApis_Admin_Server_InfoV1
 *      /api/admin/server/info?v=2      => MApis_Admin_Server_InfoV2
 */
class MCore_Web_Router
{
    private static $router_rule_list = array();
    private static $path_map_list = array();
    private static $pre_path_map_list = array();
    private static $api_class_list = array();

    const PREFIX_API_PATH = 'api/';
    const PREFIX_APIS = 'MApis_';
    const PREFIX_PAGES = 'MApps_';

    /**
     * A list to mark the exsitent API class.
     */
    public static function addApiClassList($list)
    {
        self::$api_class_list += $list;
    }

    /**
     * Map a path to another, by a map list.
     */
    public static function addPathMapList($list)
    {
        foreach ($list as $origin_path => $mapped_path)
        {
            self::addPathMap($origin_path, $mapped_path);
        }
    }

    /**
     * Map a path to another.
     */
    public static function addPathMap($origin_path, $mapped_path)
    {
        $origin_path = self::tidyPath($origin_path);
        $mapped_path = self::tidyPath($mapped_path);
        self::$path_map_list[$origin_path] = $mapped_path;
    }

    /**
     * Prefix path map
     * {'img' => 'common/image.php'}
     */
    public static function addPrePathMap($list)
    {
        foreach ($list as $pre => $path)
        {
            self::$pre_path_map_list[self::tidyPath($pre)] = self::tidyPath($path);
        }
    }

    /**
     * Add rule for extract arguments from path
     */
    public static function addRule()
    {
        $args = func_get_args();
        if (is_array($args[0]))
        {
            foreach ($args[0] as $path => $arg_keys)
            {
                self::$router_rule_list[self::tidyPath($path)] = $arg_keys;
            }
        }
        else
        {
            self::$router_rule_list[self::tidyPath($args[0])] = $args[1];
        }
    }

    /**
     * neither not slash begin with and, nor end with
     */
    private static function tidyPath($path)
    {
        // remove slash if the path begins with
        if (strpos($path, DS) === 0)
        {
            $path = substr($path, 1);
        }

        // remove the slash if it ends with
        if (substr($path, -1) == DS)
        {
            $path = substr($path, 0, -1);
        }
        return $path;
    }

    public static function fetchRequestInfoFromUrl($origin_url)
    {
        $reuqest_info = array();
        $reuqest_info['origin_url'] = $origin_url;

        list($path, $post_path) = explode('.', $origin_url);

        $path = self::tidyPath($path);

        // fetch arguments for specified path
        $argv = array();
        foreach (self::$router_rule_list as $want_path => $arg_keys)
        {
            if (strpos($path, $want_path . DS) === 0)
            {
                $values = explode(DS, substr($path, strlen($want_path) + 1));
                $path = $want_path;
                foreach ($arg_keys as $index => $key)
                {
                    if (isset($values[$index]))
                    {
                        $argv[$key] = $values[$index];
                    }
                }
                break;
            }
        }

        // check path map
        if (isset(self::$path_map_list[$path]))
        {
            $reuqest_info['origin_path'] = DS . $path;
            $path = self::$path_map_list[$path];
        }

        // check path prefix
        foreach (self::$pre_path_map_list as $pre => $mapped_path)
        {
            if (strpos($path, $pre) === 0)
            {
                $reuqest_info['origin_path'] = DS . $path;
                $path = $mapped_path;
                break;
            }
        }

        // process the API version
        if (strpos($path, self::PREFIX_API_PATH) === 0 && $path = substr($path, strlen(self::PREFIX_API_PATH)))
        {
            $reuqest_info['is_api'] = true;
            $class_name = self::PREFIX_APIS . self::classNameFromPath($path);
            $v = MCore_Tool_Input::clean('r', 'v', 'int');
            while ($v > 0)
            {
                $api_class_name = $class_name . 'V' . $v;
                if (isset(self::$api_class_list[$api_class_name]))
                {
                    $class_name = $api_class_name;
                    break;
                }
                else
                {
                    $v--;
                }
            }
        }
        else
        {
            $class_name = self::PREFIX_PAGES . self::classNameFromPath($path);
        }

        $reuqest_info['argv'] = $argv;
        $reuqest_info['path'] = DS . $path;

        if ($post_path)
        {
            $reuqest_info['path'] .= '.' . $post_path;
        }

        $reuqest_info['class_name'] = $class_name;
        return $reuqest_info;
    }

    /**
     * Build class name from given path
     */
    private static function classNameFromPath($path)
    {
        $str_list = explode(DS, $path);
        if (strpos($path, HYPHEN) !== false)
        {
            foreach ($str_list as $index => $str)
            {
                if (strpos($str, HYPHEN) !== false)
                {
                    $str_list[$index] = implode('', array_map('ucfirst', explode(HYPHEN, $str)));
                }
            }
        }
        return implode(UNDERSCORE, array_map('ucfirst', $str_list));
    }
}
