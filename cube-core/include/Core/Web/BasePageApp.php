<?php
/**
 *  Page basic class
 *
 * @author      huqiu
 */
abstract class MCore_Web_BasePageApp extends MCore_Web_BaseApp
{
    private $view;
    private $resTool;

    protected abstract function createView();
    protected abstract function outputHttp();
    protected abstract function outputBody();

    protected function output()
    {
        $this->outputHttp();
        $this->outputBody();
    }

    protected function getResTool()
    {
        if (!$this->resTool)
        {
            $this->resTool = $this->createResTool();
        }
        return $this->resTool;
    }

    protected function createResTool()
    {
        return new MCore_Tool_CssJs();
    }

    public function getView()
    {
        if (!$this->view)
        {
            $this->view = $this->createView();
        }
        return $this->view;
    }

    protected function go2($url, $data = array())
    {
        $url = MCore_Str_Url::buildUrl($data, $url);
        header("Location: $url");
        exit;
    }

    protected function processException($ex)
    {
        $msg = '<pre>' . $ex->getMessage() .'</pre>';
        if (!MCore_Tool_Env::isProd())
        {
            $msg .= '<pre>' . $ex->getTraceAsString() . '</pre>';
        }
        echo $msg;
    }
}
