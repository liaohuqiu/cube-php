<?php
class MAdmin_Views_ItemActionController
{
    protected $backUrl;
    protected $action;

    protected $inputInfo = array();
    protected $identityInfo = array();
    protected $onItemAction;

    public function __construct($identityKeys, $inputKeys, $onItemAction)
    {
        $this->onItemAction = $onItemAction;
        $this->action = MCore_Tool_Input::clean('r', 'edit_action_name', 'str');
        $this->backUrl = MCore_Tool_Input::clean('r', 'refer', 'str');

        foreach ($identityKeys as $key)
        {
            $value = MCore_Tool_Input::clean('r', $key, 'str');
            $this->identityInfo[$key] = $value;
        }

        foreach ($inputKeys as $key)
        {
            $this->inputInfo[$key] = MCore_Tool_Input::clean('r', $key, 'str');
        }
    }

    public function dispatch()
    {
        if ('submit' == $this->action)
        {
            $this->onItemAction->onSubmit($this->identityInfo, $this->inputInfo);
            $this->goBack();
        }
        else if ('delete' == $this->action || 'del' == $this->action)
        {
            $this->onItemAction->onDelete($this->identityInfo);
            $this->goBack();
        }
        else
        {
            $this->onItemAction->onEdit($this->identityInfo);
        }
    }

    public function getIdentityInfo()
    {
        return $this->getIdentityInfo;
    }

    public function getInputInfo()
    {
        return $this->inputInfo;
    }

    public function goBack($url = '')
    {
        !$url && $url = $this->backUrl;
        if (!$url)
        {
            return false;
        }
        header("Location:" . $url);
        return true;
    }
}
