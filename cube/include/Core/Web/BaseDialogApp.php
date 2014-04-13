<?php
/**
 *
 * @author      huqiu
 */
class MCore_Web_App_DialogBase extends MCore_Web_App_AppBase
{
    protected $dialog;
    private $_smartyData = array();
    private $_smartyTemplate;

    protected function init()
    {
        parent::init();
        $this->dialog = new MCore_Web_View_Dialog();
    }

    public function setBody($data, $smartyTemplate)
    {
        $data['uniqueId'] = $this->dialog->getId();
        foreach ($data as $key => $value)
        {
            $this->getInnerView()->setPageData($key, $value);
        }
        $body = $this->getInnerView()->fetch($smartyTemplate);
        $this->dialog->setBody($body);
    }

    protected function outputBody()
    {
        $this->dialog->show();
    }

    protected function showError($ex)
    {
        $this->dialog->showError($ex);
    }
}
?>
