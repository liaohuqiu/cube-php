<?php
/**
 *
 * @author huqiu
 */
class MCore_Web_View
{
    private $data = array();
    private $baseData = array();
    private $pageData = array();
    private $viewDisplayer;

    public function __construct(MCore_Web_IViewDisplayer $viewDisplayer)
    {
        $this->viewDisplayer = $viewDisplayer;
    }

    public function setBaseData()
    {
        $argc = func_num_args();
        $argv = func_get_args();
        if ($argc == 1)
        {
            $this->baseData = array_merge($this->baseData, $argv[0]);
        }
        else
        {
            $this->baseData[$argv[0]] = $argv[1];
        }
        return $this;
    }

    public function setData()
    {
        $argc = func_num_args();
        $argv = func_get_args();
        if ($argc == 1)
        {
            $this->data = array_merge($this->data, $argv[0]);
        }
        else
        {
            if ($argv[0] == 'page_data')
            {
                throw new Exception('You can not set pageData by call setData(\'page_data\', $data), call setPageData($data) instead.');
            }
            $this->data[$argv[0]] = $argv[1];
        }
        return $this;
    }

    public function setPageData()
    {
        $argc = func_num_args();
        $argv = func_get_args();
        if ($argc == 1)
        {
            if (!is_array($argv[0]))
            {
                throw new Exception('setPageData error');
            }
            $this->pageData = array_merge($this->pageData, $argv[0]);
        }
        else
        {
            $this->pageData[$argv[0]] = $argv[1];
        }
        return $this;
    }

    private function processData()
    {
        $data = array_merge($this->baseData, $this->data);
        $data['page_data'] = $this->pageData;
        foreach ($data as $key => $value)
        {
            $this->viewDisplayer->setData($key, $value);
        }
    }

    public function addDir($path)
    {
        $this->viewDisplayer->addDir($path);
    }

    public function display($templatePath)
    {
        $this->processData();
        $this->viewDisplayer->display($templatePath);
    }

    public function render($templatePath)
    {
        $this->processData();
        $output = $this->viewDisplayer->render($templatePath);
        return $output;
    }
}
