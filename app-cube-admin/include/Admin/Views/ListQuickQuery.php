<?php
class MAdmin_Views_ListQuickQuery
{
    private $itemList =  array(
        // 'cate' => array(
        //     'des' => 'Category',
        //     'default_value' => 0,
        //     'value_name_map' => ,
        //     'name_value_map' => ,
        );

    public function __construct($itemList)
    {
        if (is_array($itemList))
        {
            $this->itemList = $itemList;
        }
    }

    public function addSearchInfo($searchInfo)
    {
    }

    public function getDefaultValues()
    {
        $list = array();
        foreach ($this->itemList as $key => $item)
        {
            if (isset($item['default_value']))
            {
                $list[$key] = $item['default_value'];
            }
        }
        return $list;
    }

    public function getSelectList($input)
    {
        $itemList = $this->itemList;
        if (empty($itemList) || !is_array($itemList))
        {
            return false;
        }
        $list = array();
        foreach ($itemList as $key => $item)
        {
            $value = $item['default_value'];
            if (isset($input[$key]))
            {
                $value = $input[$key];
            }

            $options = $item['name_value_map'];
            if (!$options)
            {
                $options = $item['value_name_map'];
                if ($options)
                {
                    $options = array_flip($options);
                }
            }
            if (!$options)
            {
                throw new Exception('the field named $key is a select field and the options is empty');
            }

            $info = array();
            $info['des'] = $item['des'];
            $info['options'] = MCore_Str_Html::options($options, $value);
            $list[$key] = $info;
        }
        return $list;
    }
}
