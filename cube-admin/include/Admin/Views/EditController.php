<?php
class MAdmin_Views_EditController
{
    protected $action;

    protected $inputInfo = array();
    protected $identityInfo = array();
    protected $itemList =  array();

    protected $backUrl;
    protected $conf;
    protected $dataProvider;

    public function __construct($conf)
    {
        $this->conf = $conf;
    }

    public function setDataProvider($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    protected function getPara()
    {
        $this->action = MCore_Tool_Input::clean('r', 'edit_action_name', 'str');
        $this->backUrl = MCore_Tool_Input::clean('r', 'refer', 'str');

        $identityKeys = $this->conf['identity_keys'];
        foreach ($identityKeys as $key)
        {
            $value = MCore_Tool_Input::clean('r', $key, 'str');
            $this->identityInfo[$key] = $value;
        }

        $allKeys = array_keys($this->conf['edit_info']);
        foreach ($allKeys as $key)
        {
            $this->inputInfo[$key] = MCore_Tool_Input::clean('r', $key, 'str');
        }
    }

    public function render()
    {
        $this->getPara();

        if('submit' == $this->action)
        {
            $this->dataProvider->submit($this->inputInfo, $this->identityInfo);
            $this->goBack();
            return;
        }
        if('delete' == $this->action || 'del' == $this->action)
        {
            $this->dataProvider->delete($this->identityInfo);
            $this->goBack();
            return;
        }
        else
        {
            $info = $this->dataProvider->getInfo($this->identityInfo);
            $editInfo = $this->conf['edit_info'];
            if (!empty($info))
            {
                foreach ($editInfo as $key=>$config)
                {
                    if (isset($info[$key]))
                    {
                        $editInfo[$key]['value'] = $info[$key];
                    }
                }
            }
            $this->itemList = $this->formatList($editInfo);
        }
    }

    public function output()
    {
        $view = MApps_AdminPageBase::createSmartyView();
        $data = array();
        $data['identity_info'] = $this->identityInfo;
        $data['post_url'] = $this->conf['post_url'];
        $data['item_list'] = $this->itemList;
        $view->setPageData($data);
        $view->display('admin/widget/edit.html');
    }

    protected function formatList($list)
    {
        foreach($list as $key => $info)
        {
            $value = $info['value'];

            $type = $info['type'];
            !$type && $type = 'text';
            if($type == 'textarea')
            {
                list($w,$h) = explode('x',$info['size']);
                !$w && $w = 443;
                !$h && $h = 53;
                $info['style'] = 'width: ' . $w .'px; height: ' . $h . 'px';
            }
            else if($type == 'select' && (!is_array($info['options']) || empty($info['options'])))
            {
                throw new Exception("the filed named $key is a select field and the options is empty");
            }
            else if($type == 'radio' && (!is_array($info['options']) || empty($info['options'])))
            {
                throw new Exception("the filed named $key is a radio field and the options is empty");
            }
            else if ($type == 'checkbox')
            {
                $info['checked'] = $value ? 'checked="true"' : '';
            }

            $info['type'] = $type;
            $info["name"] =  $key;
            $info['id'] = "__j_id_$key";

            $list[$key] = $info;
        }
        return $list;
    }

    protected function goBack($url = '')
    {
        !$url && $url = $this->backUrl;
        if(!$url)
        {
            return false;
        }
        header("Location:" . $url);
        return true;
    }
}
