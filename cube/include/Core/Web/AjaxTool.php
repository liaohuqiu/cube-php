<?php
/**
 *  core/ajax/Request.js
 *
 *  on succ will output:
 *      array(
 *          'ok' => true,
 *          'data' => ...,
 *          'cacheTime' => null / int
 *          );
 *
 *  on error will output:
 *      array(
 *          'error' => true,
 *          'errorMsg' => '...',
 *          );
 *
 * @author      huqiu
 */
class MCore_Web_AjaxTool extends MCore_Web_JsonTool
{
    private $error = false;
    private $errorMsg = '';
    private $handler;

    private $cacheTime;

    private $jsList = array();
    private $cssList = array();
    private $onloads = array();

    public function go2($url)
    {
        $onload = 'window.location="' . htmlspecialchars($url) . '";';
        $this->addOnload($onload);
        return $this;
    }

    public function setCache($cacheTime = 86400)
    {
        $this->cacheTime = $cacheTime;
        return $this;
    }

    public function addJs($strOrList)
    {
        $this->jsList = array_merge($this->jsList, (array) $strOrList);
        return $this;
    }

    public function addCss($strOrList)
    {
        $this->cssList = array_merge($this->cssList, (array) $strOrList);
        return $this;
    }

    public function addOnload($onload)
    {
        if (is_object($onload))
        {
            $onload = $onload->__toString();
        }

        $this->onloads = array_merge($this->onloads, (array) $onload);
        return $this;
    }

    private function getOutputData()
    {
        $data = array();
        $data['data'] = $this->data;
        if (!$this->error)
        {
            $res = $this->getResource();
            $res && $data['resource'] = $res;
            $this->handler && $data["handler"] = $this->handler;
            $this->cacheTime && $data["cacheTime"] = $this->cacheTime;
            $data['ok'] = true;
        }
        else
        {
            $data['error'] = $this->error;
            $data['errorMsg'] = $this->errorMsg;
        }

        $data = self::encode($data);
        return $data;
    }

    public function output()
    {
        $output = $this->getOutputData();
        if (!headers_sent())
        {
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        }

        if (!$output && !headers_sent())
        {
            header("HTTP/1.0 204 No Content");
        }
        else
        {
            echo $output;
        }
        exit;
    }

    public function processException($ex)
    {
        $errorMsg = $ex->getMessage();
        if (!MCore_Tool_Env::isProd())
        {
            $traceStr = $ex->getTraceAsString();
            $trace = var_export($ex->getTrace(), true);
            $errorMsg .= "\n\n$traceStr\n\n$trace";
            $errorMsg = '<pre>' . $errorMsg . '</pre>';
        }

        $this->setError($errorMsg);
        $this->output();
    }

    public function setError($msg)
    {
        $this->error = true;
        $this->errorMsg = $msg;
    }

    private function getResource()
    {
        $resource = array();
        $resource["onloads"] = $this->onloads;
        $resource["js"] =  array();
        $resource["css"] = array();

        $resource = array_filter($resource);
        return $resource;
    }
}
