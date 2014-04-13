<?php
/**
 *
 * A pop up dialog
 *
 * @author huqiu
 */
class MCore_Web_DialogView
{
    private $_id;
    private $_ajaxTool;
    private $_title = "";
    private $_noTitle = false;
    private $_body = "";
    private $_width = 350;
    private $_height = 100;
    private $_metaDataContainer;
    private $_handler = "";

    public function __construct($title = "")
    {
        $this->_id = time() . rand(0, 10000);
        $this->_title = $title;
        $this->_ajaxTool = new MCore_Web_AjaxTool();
        $this->_metaDataContainer = new MCore_Util_DataContainer();
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getAjaxTool()
    {
        return $this->_ajaxTool;
    }

    public function noTitle($noTitle = true)
    {
        $this->_noTitle = $noTitle;
        return $this;
    }

    public function setCache($cacheTime = 86400)
    {
        $this->_ajaxTool->setCache($cacheTime);
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function setWidth($width)
    {
        $this->_width = $width;
        return $this;
    }

    public function setHeight($height)
    {
        $this->_height = $height;
        return $this;
    }

    public function setSize($width, $height)
    {
        $this->setWidth($width);
        $this->setHeight($height);
        return $this;
    }

    public function setData($data)
    {
        $this->_metaDataContainer->setData($data);
        return $this;
    }

    public function setHandler($handlerName)
    {
        $trans = array("js/" => "", ".js" => "");
        $handlerName = strtr($handlerName, $trans);

        $this->_handler = $handlerName;
        $this->_ajaxTool->addJs("js/" . $handlerName . ".js");
        return $this;
    }

    public function show()
    {
        $this->_title && $this->_noTitle = false;

        $dialog = array();
        $dialog["title"] = $this->_title;
        $dialog["noTitle"] = $this->_noTitle;
        $dialog["body"] = $this->_body;
        $dialog["width"] = $this->_width;
        $dialog["height"] = $this->_height;
        $dialog["handler"] = $this->_handler;
        $dialog["metaData"] = $this->_metaDataContainer->getData();

        $this->_ajaxTool->setData("dialog", $dialog);
        $this->_ajaxTool->output();
    }

    public function showError($ex)
    {
        $msg = '<pre>' . $ex->getMessage() .'</pre>';
        if (MCore_Tool_Env::isTest())
        {
            $msg .= '<pre>' . $ex->getTraceAsString() . '</pre>';
        }
        $this->_ajaxTool->outputError($msg);
    }
}
?>
