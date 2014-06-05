<?php
/**
 * Basic Page for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminPageBase extends MCore_Web_BasePageApp
{
    protected $user;

    protected $host;
    protected $moduleMan;

    protected function checkAuth()
    {
        if (!MAdmin_Init::checkInit())
        {
            $this->go2('/init');
        }
        $user = MAdmin_UserAuth::getUser();
        if (!$user)
        {
            $this->go2('/admin/user-login');
        }
        $this->user = $user;
        $this->moduleMan = new MAdmin_Module($this->request->getPath(), $user);
        if (!$this->moduleMan->userHasAuth())
        {
            $this->go2('/admin');
        }
    }

    protected function outputHttp()
    {
        header('Content-type: text/html; charset=utf-8');
    }

    protected function init()
    {
        $this->getResTool()->addCss('admin/admin-base.css');
        $this->getResTool()->addFootJs('admin/AAdminGlobal.js');
    }

    protected function getTitle()
    {
        $module = $this->moduleMan->getCurrentModuleInfo();
        if ($module)
        {
            return '';
        }
        return $module['name'];
    }

    public static function createDisplayView()
    {
        $viewDisplyer = new MCore_Web_SimpleView(CUBE_DEV_ROOT_DIR . '/template');
        $view = new MCore_Web_View($viewDisplyer);
        $baseData = array();
        $baseData['static_pre_path'] = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 'static_res_host') . '/cube-admin-mix';
        $view->setBaseData('base_data', $baseData);
        return $view;
    }

    protected function createView()
    {
        return self::createDisplayView();
    }

    protected function output()
    {
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

        if ($this->user)
        {
            $header_data['user'] = $this->user->toArray();
            $header_data['title'] = $this->getTitle();

            $header_data['module_list'] = $this->moduleMan->getModuleList();
            $header_data['module_info'] = $this->moduleMan->getCurrentModuleInfo();
            $header_data['base_path'] = $this->moduleMan->getBasePath() . DS;
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
}
