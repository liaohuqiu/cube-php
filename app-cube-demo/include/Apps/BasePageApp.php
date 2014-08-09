<?php
abstract class MApps_BasePageApp extends MCore_Web_BasePageApp
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
        // header('Content-type: text/html; charset=utf-8');
        // header("Cache-Control: no-cache");
    }

    protected function init()
    {
        // $this->getResTool()->addCss('mw/mw-base.css');
        // $this->getResTool()->addCss('mw/mw-index.css');
        // $this->getResTool()->addFootJs('mw/AmwGlobal.js');
    }

    protected function getTitle()
    {
        return 'title';
    }

    public static function createDisplayView()
    {
        // $viewDisplyer = new MCore_Web_SimpleView(ROOT_DIR . '/template');
        // $view = new MCore_Web_View($viewDisplyer);
        // $baseData = array();
        // $baseData['static_pre_path'] = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 'static_res_host') . '/mw-static';
        // $view->setBaseData('base_data', $baseData);
        // return $view;
    }

    protected function createView()
    {
        // return self::createDisplayView();
    }

    protected function output()
    {
        // $this->beginOutput = true;
        // $this->outputHttp();
        // $this->outputHead();
        // $this->outputBody();
        // $this->outputTail();
    }

    protected function outputHead()
    {
        // $header_data = array();
        // $header_data['css_html'] = $this->getResTool()->getCssHtml();
        // $header_data['js_html'] = $this->getResTool()->getHeadJsHtml();
        // $header_data['is_index'] = true;

        // if ($this->userData)
        // {
        //     // $header_data['proxy_auth'] = MAdmin_UserAuth::hasAuthProxy();
        //     // $header_data['right_links'] = MAdmin_UserAuth::getRightLinks();
        //     // $header_data['user_data'] = $this->userData->getData();
        //     // $header_data['title'] = $this->getTitle();

        //     // $header_data['module_list'] = $this->moduleMan->getModuleList();
        //     // $header_data['module_info'] = $this->moduleMan->getCurrentModuleInfo();
        //     // $header_data['base_path'] = $this->moduleMan->getBasePath() . DS;
        // }

        // $this->getView()->setData('header_data', $header_data);
        // $this->getView()->display('app/base/head.html');
    }

    protected function outputTail()
    {
        $tailData = array();
        $tailData['js_html'] = $this->getResTool()->getTailJsHtml();
        $this->getView()->setData('tail_data', $tailData)->display('app/base/tail.html');
    }

    protected function outputBody()
    {
    }

    protected function processException1($ex)
    {
        // if (!$this->beginOutput)
        // {
        //     $this->outputHttp();
        //     $this->outputHead();
        // }
        // $page_data = array();
        // $page_data['msg'] = $ex->getMessage();
        // if (!MCore_Tool_Env::isProd())
        // {
        //     $page_data['trace_str'] = $ex->getTraceAsString();
        //     $page_data['trace'] = var_export($ex->getTrace(), true);
        // }
        // $this->getView()->setPageData($page_data)->display('app/base/error.html');

        // if (!$this->beginOutput)
        // {
        //     $this->outputTail();
        // }
    }
}
