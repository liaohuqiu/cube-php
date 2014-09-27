<?php
/**
 *
 * A pop up dialog
 *
 * @author huqiu
 */
class MCore_Web_DialogView
{
    private $id;
    private $ajaxTool;
    private $title = "";
    private $noTitle = false;
    private $body = "";
    private $width = 350;
    private $height = 100;
    private $metaDataContainer;
    private $property;
    private $handler = "";

    public function __construct($title = "")
    {
        $this->id = time() . rand(0, 10000);
        $this->title = $title;
        $this->ajaxTool = new MCore_Web_AjaxTool();
        $this->metaDataContainer = new MCore_Util_DataContainer();
        $this->property = new MCore_Util_DataContainer();
    }

    public function getId()
    {
        return $this->id;
    }

    public function noTitle($noTitle = true)
    {
        $this->noTitle = $noTitle;
        return $this;
    }

    public function setCache($cacheTime = 86400)
    {
        $this->ajaxTool->setCache($cacheTime);
        return $this;
    }

    public function setProperty()
    {
        $this->property->setFuncArgsData(func_get_args());
        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    public function setHeight($height)
    {
        $this->height = $height;
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
        $this->metaDataContainer->setData($data);
        return $this;
    }

    public function setHandler($handlerName)
    {
        $trans = array("js/" => "", ".js" => "");
        $handlerName = strtr($handlerName, $trans);

        $this->handler = $handlerName;
        $this->ajaxTool->addJs("js/" . $handlerName . ".js");
        return $this;
    }

    public function show()
    {
        $this->title && $this->noTitle = false;

        $dialog = array();
        $dialog["title"] = $this->title;
        $dialog["noTitle"] = $this->noTitle;
        $dialog["body"] = $this->body;
        $dialog["width"] = $this->width;
        $dialog["height"] = $this->height;
        $dialog["handler"] = $this->handler;
        $dialog["metaData"] = $this->metaDataContainer->getData();
        $dialog["dialogProperty"] = $this->property->getData();

        $this->ajaxTool->setData("dialog", $dialog);
        $this->ajaxTool->output();
    }

    public function processException($ex)
    {
        $msg = '<pre>' . $ex->getMessage() .'</pre>';
        if (!MCore_Tool_Env::isProd())
        {
            $msg .= '<pre>' . $ex->getTraceAsString() . '</pre>';
        }
        $this->ajaxTool->setError($msg);
        $this->ajaxTool->output();
    }

    public function getAjaxTool()
    {
        return $this->ajaxTool;
    }
}
