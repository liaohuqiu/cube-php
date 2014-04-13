<?php
class MWeb_PageBase extends MCore_Web_App_PageBase
{
    protected $user;

    protected function checkAuth()
    {
        $user = MUser_UserManager::tryGetUser();
        $this->user = $user;
    }

    protected function createWebView()
    {
        $data = array();

        $commonData = array();
        $commonData['baseInfo'] = $data;
        $templateDir = TEMPLATE_DIR;
        return new MCore_Web_View_WebView($templateDir, $commonData);
    }

    protected function outputHttp()
    {
        header('Content-type: text/html; charset=utf-8');
    }

    protected function outputHead()
    {
        $headData = array();
        $headData['cssHtml'] = $this->getResTool()->getCssHtml();
        $headData['jsHtml'] = $this->getResTool()->getHeadJsHtml();
        $headData['title'] = $this->getTitle();
        $this->getWebView()->setData('headData', $headData);
        $this->getWebView()->display('base/head.html');
    }

    protected function getTitle()
    {
        return 'Share';
    }

    protected function outputTail()
    {
        $tailData = array();
        $tailData['jsHtml'] = $this->getResTool()->getTailJsHtml();
        $pageJsDataHtml = $this->getResTool()->getPageJsDataHtml();
        if ($pageJsDataHtml)
        {
            $tailData['pageJsDataHtml'] = $pageJsDataHtml;
        }

        $this->getWebView()->setData('tailData', $tailData)->display('base/tail.html');
    }
}
