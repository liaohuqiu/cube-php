<?php
/**
 * Name - Value list
 */
class MAdmin_Views_ListQuickQuery
{
    private $itemList =  array(
        // 'cate' => array(
        //     'des' => 'Category',
        //     'type' => 'select',
        //     'defaultValue' => 0,
        //     'options' => $topCatOptions,
        );

    public function __construct($itemList)
    {
        $this->itemList = $itemList;
    }

    public function addSearchInfo($searchInfo)
    {
    }

    public function getQueryList($pageInputData)
    {
        $itemList = $this->itemList;
        if (empty($itemList) || !is_array($itemList))
        {
            return false;
        }
        $list = array();
        foreach ($itemList as $fieldKey => $info)
        {
            $info['field'] = $fieldKey;

            $currentValue = $info['defaultValue'];
            if (isset($pageInputData[$fieldKey]))
            {
                $currentValue = $pageInputData[$fieldKey];
                $info['value'] = $currentValue;
            }

            $type = $info['type'];
            if ($type == 'select')
            {
                $options = $info['options'];
                if (!$options)
                {
                    throw new Exception('the field named $fieldKey is a select field and the options is empty');
                }
                $info['options'] = MCore_Str_Html::options($options, $currentValue);
            }
            $list[] = $info;
        }
        return $list;
    }
}
