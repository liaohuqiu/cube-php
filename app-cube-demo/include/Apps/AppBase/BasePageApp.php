<?php
abstract class MApps_AppBase_BasePageApp extends MCore_Web_BasePageApp
{
    protected $userData;

    protected $host;
    protected $moduleMan;

    private $beginOutput = false;

    protected function checkAuth()
    {
    }

    protected function outputHttp()
    {
        //TODO
        header('Content-type: text/html; charset=utf-8');
        header("Cache-Control: no-cache");
    }

    protected function init()
    {
        $this->getResTool()->addCss('cube-demo/base.css');
        $this->getResTool()->addFootJs('cube-demo/AGlobal.js');
    }

    protected function getTitle()
    {
        return 'Cube Demo';
    }

    public static function createDisplayView()
    {
        $viewDisplyer = new MCore_Web_SimpleView(APP_ROOT_DIR . '/template');
        $view = new MCore_Web_View($viewDisplyer);
        $baseData = array();
        $baseData['static_pre_path'] = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 's_host') . '/cube-demo-static';
        $view->setBaseData('base_data', $baseData);
        return $view;
    }

    protected function createView()
    {
        return self::createDisplayView();
    }

    protected function output()
    {
        $this->beginOutput = true;
        $this->outputHttp();
        $this->outputHead();
        $this->outputBody();
        $this->outputTail();
    }

    protected function outputHead()
    {
        $header_data = array();
        $header_data['title'] = $this->getTitle();
        $header_data['css_html'] = $this->getResTool()->getCssHtml();
        $header_data['js_html'] = $this->getResTool()->getHeadJsHtml();

        $this->getView()->setData('header_data', $header_data);
        $this->getView()->display('base/head.html');
    }

    protected function outputTail()
    {
        $tailData = array();
        $tailData['js_html'] = $this->getResTool()->getTailJsHtml();
        $this->getView()->setData('tail_data', $tailData)->display('base/tail.html');
    }

    protected function outputBody()
    {
    }

    protected function processException($ex)
    {
        if (!$this->beginOutput)
        {
            $this->outputHttp();
            $this->outputHead();
        }
        $page_data = array();
        $page_data['msg'] = $ex->getMessage();
        if (!MCore_Tool_Env::isProd())
        {
            $page_data['trace_str'] = $ex->getTraceAsString();
            $page_data['trace'] = var_export($ex->getTrace(), true);
        }
        $this->getView()->setPageData($page_data)->display('base/error.html');

        if (!$this->beginOutput)
        {
            $this->outputTail();
        }
    }
}
