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
 *          'error_msg' => '...',
 *          'error_code' => '...',
 *          'error_code' => '...',
 *          );
 *
 * @author      huqiu
 */
class MCore_Web_AjaxTool extends MCore_Web_JsonTool
{
    private $error = false;
    private $errorMsg = '';
    private $errorCode = 0;
    private $errorTrace = null;
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
            $this->cacheTime && $data["cache_time"] = $this->cacheTime;
            $data['ok'] = true;
        }
        else
        {
            $data['error'] = $this->error;
            $data['error_msg'] = $this->errorMsg;
            $data['error_code'] = $this->errorCode;
            $this->errorTrace && $data['error_trace'] = $this->errorTrace;
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
        $trace = null;
        if (!MCore_Tool_Env::isProd())
        {
            $traceStr = $ex->getTraceAsString();
            $trace = var_export($ex->getTrace(), true);
            $trace = "<pre>$traceStr\n\n$trace</pre>";
        }

        $this->setError($errorMsg, $ex->getCode(), $trace);
        $this->output();
    }

    public function setError($msg, $code = 0, $trace = null)
    {
        $this->error = true;
        $this->errorMsg = $msg;
        $this->errorCode = $code;
        $this->errorTrace = $trace;
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
