<?php
/**
 * Basic Page for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminPageBase extends MCore_Web_BasePageApp
{
    protected $userData;

    protected $host;
    protected $moduleMan;

    private $beginOutput = false;
    private $error = false;
    private $show_left_side = true;

    protected function checkAuth()
    {
        if (!MAdmin_Init::checkInit())
        {
            $this->go2('/init');
        }
        $userData = MAdmin_UserAuth::checkLoginByGetUser();
        if (!$userData)
        {
            $this->go2('/admin/user/login');
        }
        $this->userData = $userData;
        $this->moduleMan = new MAdmin_Module($this->request->getPath(), $userData);
        if (!$this->moduleMan->userHasAuth())
        {
            $this->go2('/admin');
        }
    }

    protected function outputHttp()
    {
        header('Content-type: text/html; charset=utf-8');
        header("Cache-Control: no-cache");
    }

    protected function init()
    {
        if (MCore_Tool_Conf::getDataConfigByEnv('mix', 'disable_admin', false))
        {
            header('Status: 403 Forbidden');
            echo '<h1>403 Forbidden</h1>';
            exit;
        }
        $this->getResTool()->addCss('admin/admin-base.css');
        $this->getResTool()->addFootJs('admin/AAdminGlobal.js');
    }

    protected function getTitle()
    {
        if (!$this->moduleMan)
        {
            return '';
        }
        $module = $this->moduleMan->getCurrentModuleInfo();
        $title = $module['current_unit']['current_item']['name'];
        return $title . ' - Cube for ' . APP_NAME;
    }

    public static function createDisplayView()
    {
        $viewDisplyer = new MCore_Web_SimpleView(ADMIN_ROOT_DIR . '/template');
        $view = new MCore_Web_View($viewDisplyer);
        $baseData = array();
        $baseData['static_pre_path'] = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 's_host') . '/cube-admin-mix';
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
        $header_data['css_html'] = $this->getResTool()->getCssHtml();
        $header_data['js_html'] = $this->getResTool()->getHeadJsHtml();

        if (!$this->error && $this->userData)
        {
            $header_data['proxy_auth'] = MAdmin_UserAuth::hasAuthProxy();
            $header_data['right_links'] = MAdmin_UserAuth::getRightLinks();
            $header_data['user_data'] = $this->userData->getData();
            $header_data['title'] = $this->getTitle();

            $module_info = $this->moduleMan->getCurrentModuleInfo();
            $this->show_left_side = $module_info && !$module_info['current_unit']['current_item']['no_left_side'];
            $header_data['module_list'] = $this->moduleMan->getModuleList();
            $header_data['module_info'] = $this->moduleMan->getCurrentModuleInfo();
            $header_data['base_path'] = $this->moduleMan->getBasePath() . DS;
            $header_data['show_left_side'] = $this->show_left_side;
        }

        $this->getView()->setData('header_data', $header_data);
        $this->getView()->display('admin/base/head.html');
    }

    protected function outputTail()
    {
        $tailData = array();
        $tailData['js_html'] = $this->getResTool()->getTailJsHtml();
        $this->getView()->setData('tail_data', $tailData)->display('admin/base/tail.html');
    }

    protected function outputBody()
    {
    }

    protected function processException($ex)
    {
        $this->error = true;
        try
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
            $this->getView()->setPageData($page_data)->display('admin/base/error.html');

            if (!$this->beginOutput)
            {
                $this->outputTail();
            }
        }
        catch (Exception $ex)
        {
            if (!MCore_Tool_Env::isProd())
            {
                echo '<pre>';
                echo $ex->getTraceAsString();
            }
            else
            {
                echo 'error when process Exception';
                exit;
            }
        }
    }
}
