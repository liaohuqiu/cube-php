<?php
/**
 *  a resuorce manager for cube-js
 *
 * @author      huqiu
 */
class MCore_Tool_CssJs
{
    private $_headJsList = array();
    private $_cssList = array();
    private $_footJsList = array();
    private $_pageJsData = array();

    public function addHeadJs($js)
    {
        $this->_headJsList = array_merge($this->_headJsList, (array) $js);
        return $this;
    }

    public function addFootJs($js)
    {
        $this->_footJsList = array_merge($this->_footJsList, (array) $js);
        return $this;
    }

    public function addCss($css)
    {
        $this->_cssList = array_merge($this->_cssList, (array)$css);
        return $this;
    }

    public function getHeadJsHtml()
    {
        $list = $this->_headJsList;

        $list[] = 'base/Cube.js';
        $list[] = 'version.js';

        $contents = array();
        foreach ($list as $js)
        {
            $url = self::getResUrl($js, 'js');
            $contents[] = '<script src="' . $url . '"></script>';
        }
        return implode("\n", $contents);
    }

    /**
     * The JS html ouputed in the tail of page.
     */
    public function getTailJsHtml()
    {
        $list = array();
        if (!empty($this->_pageJsData))
        {
            $jsonData = json_encode($this->_pageJsData);
            $list[] = "K.data.set($jsonData);";
        }

        $outerLinks = array();
        if (!empty($this->_footJsList))
        {
            // for js Debug
            if (!MCore_Tool_Env::isProd())
            {
                $list[] = 'K.__debug = 1;';
            }
            $list[] = 'K.Resource.setResPrePath("' . self::getResPrePath() . '");';
            foreach ($this->_footJsList as $js)
            {
                $modulePath = self::tryFindModulePath($js, 'js');
                if ($modulePath === false)
                {
                    $js = self::getResUrl($js, 'js');
                    $outerLinks[] = "<script src='$js' type='text/javascript'></script>";
                }
                else
                {
                    $module = self::formatModuleName($js);
                    $list[] = 'Module.load("' . $module . '");';
                }
            }
        }

        $html = '';
        if (!empty($list))
        {
            $jsHtml = implode('', $list);
            $html .= "<script>$jsHtml</script>";
        }
        if (!empty($outerLinks))
        {
            $html .= implode('', $outerLinks);
        }
        return $html;
    }

    public function getCssHtml()
    {
        $html = '';
        $cssList = $this->_cssList;
        $cssList[] = 'base.css';
        $list = array();
        foreach ($cssList as $css)
        {
            $url = self::getResUrl($css, 'css');
            $list[] = '<link href="' . $url . '" rel="stylesheet" />';
        }
        $html = implode("\n", $list);
        return $html;
    }

    public function setPageJsData()
    {
        $argv = func_get_args();
        $argc = func_num_args();
        if ($argc == 1)
        {
            $this->_pageJsData = array_merge($this->_pageJsData, $argv[0]);
        }
        else
        {
            $this->_pageJsData[$argv[0]] = $argv[1];
        }
    }

    /**
     * http://s_host/res
     */
    public static function getResPrePath()
    {
        static $path;
        if (!$path)
        {
            $path = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 's_host') . '/res';
        }
        return $path;
    }

    /**
     * http://s_host
     */
    public static function getResRootPath()
    {
        static $path;
        if (!$path)
        {
            $path = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 's_host');
        }
        return $path;
    }

    public static function getResList()
    {
        static $info;
        if (!$info)
        {
            $info = MCore_Tool_Conf::getDataConfig('res-info');
        }
        return $info;
    }

    private static function isOuterLink($path)
    {
        return strpos($path, "http") === 0 || strpos($path, "//") === 0;
    }

    private static function tryFindModulePath($path, $type)
    {
        if (self::isOuterLink($path))
        {
            return false;
        }

        $moduleId = self::formatModuleName($path);
        $resList = self::getResList();
        if (isset($resList[$type][$moduleId]))
        {
            return $resList[$type][$moduleId];
        }
        return false;
    }

    public static function getResUrl($path, $type)
    {
        $modulePath = self::tryFindModulePath($path, $type);
        if ($modulePath === false)
        {
            if (self::isOuterLink($path))
            {
                return $path;
            }
            else
            {
                strpos($path, '/') !== 0 && $path = '/' . $path;
                return self::getResRootPath() . $path;
            }
        }
        else
        {
            return self::getResPrePath() . $modulePath;
        }
    }

    public static function formatModuleName($name)
    {
        // remove "/" if starts with it;
        strpos($name, '/') === 0 && $name == substr($name, 1);

        // remove type from end
        $pos = strpos($name, '.');
        if ($pos !== false)
        {
            $name = substr($name, 0, $pos);
        }
        return $name;
    }
}
