<?php
/**
 *
 */
class MCore_Web_SimpleView implements ArrayAccess, MCore_Web_IViewDisplayer
{
    private $dir_list = array();
    private $page_data = array();

    public function __construct($dir)
    {
        substr($dir, -1) == '/' && $dir = substr($dir, 0, -1);
        $this->dir_list[$dir] = 1;
    }

    public function addDir($path)
    {
        foreach (array($path) as $dir)
        {
            substr($dir, -1) == '/' && $dir = substr($dir, 0, -1);
            $this->dir_list[$dir] = 1;
        }
    }

    public function setData($key, $value)
    {
        $this->page_data = MCore_Util_DataContainer::mergeFuncArgsData(func_get_args(), $this->page_data);
    }

    public function display($template)
    {
        $path = $this->getTemplate($template);

        if (file_exists($path))
        {
            $view = $this;
            extract($this->getPageData());
            include $path;
        }
        else
        {
            throw new Exception('can not find template: ' . $path);
        }
    }

    private function getPageData()
    {
        $list = array();
        foreach ($this->page_data as $key => $value)
        {
            $list[$key] = new MCore_Web_ViewVar($value);
        }
        return $list;
    }

    private function getTemplate($template)
    {
        if (substr($template, 0, 1) != '/')
        {
            $template = DS . $template . '.php';
        }
        foreach ($this->dir_list as $dir => $i1)
        {
            $path = $dir . $template;
            if (file_exists($path))
            {
                return $path;
            }
        }
        throw new Exception('Can not find template: ' . $template);
    }

    public function render($template)
    {
        ob_start();
        $this->display($template);
        return ob_get_clean();
    }

    /**
     * Implements ArrayAccess to quickly access $page_data.
     */
    public function offsetExists($offset)
    {
        return isset($this->page_data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->page_data[$offset]))
        {
            return new MCore_Web_ViewVar($this->page_data[$offset]);
        }
        else
        {
            throw new Exception('Index ' . $offsetSet . ' is not exsit');
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            throw new Exception('You must specify a key to this value');
        }
        else
        {
            $this->page_data[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->page_data[$offset]);
    }
}
