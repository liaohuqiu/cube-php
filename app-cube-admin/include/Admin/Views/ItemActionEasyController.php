<?php
/**
 * This class provides a edit view to display the data form data provider
 *
 * @author http://www.liaohuqiu.net
 */
class MAdmin_Views_ItemActionEasyController extends MAdmin_Views_ItemActionController
{
    protected $conf;
    protected $dataProvider;
    protected $itemList;

    public function __construct($conf, MAdmin_Views_ItemActionDataProvider $dataProvider)
    {
        $this->conf = $conf;
        $this->dataProvider = $dataProvider;
        $allKeys = array_keys($this->conf['edit_info']);
        parent::__construct($conf['identity_keys'], $allKeys, $this);
    }

    public function onSubmit($identityInfo, $inputInfo)
    {
        $this->dataProvider->submit($this->identityInfo, $this->inputInfo);
        $this->goBack();
    }

    public function onDelete($identityInfo)
    {
        $this->dataProvider->delete($this->identityInfo);
        $this->goBack();
    }

    public function onEdit($identityInfo)
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

    public function output()
    {
        $view = MApps_AdminPageBase::createDisplayView();
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
}
