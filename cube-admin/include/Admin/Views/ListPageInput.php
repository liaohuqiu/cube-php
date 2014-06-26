<?php
class MAdmin_Views_ListPageInput extends MCore_Util_ArrayLike
{
    protected $pageLocatorKeys = array(
        'pageinfo_sortby',
        'pageinfo_order',
        'pageinfo_start',
        'pageinfo_num_perpage',
        'search_value',
    );

    private $tableInput = array();
    private $pageIdentityData = array();

    public function __construct()
    {
        $start = MCore_Tool_Input::clean('r', 'pageinfo_start', 'int');
        $num_perpage = MCore_Tool_Input::clean('r', 'pageinfo_num_perpage', 'int');
        !$num_perpage && $num_perpage = 20;

        $this['pageinfo_start'] = $start;
        $this['pageinfo_num_perpage'] = $num_perpage;

        $this['pageinfo_sortby'] = MCore_Tool_Input::clean('r', 'pageinfo_sortby', 'str');
        $this['pageinfo_order'] = MCore_Tool_Input::clean('r', 'pageinfo_order', 'str');
        $this['search_value'] = MCore_Tool_Input::clean('r', 'search_value', 'str');
        $this['action_name'] = MCore_Tool_Input::clean('r', 'action_name', 'str');

        $this['pageinfo_order_reverse'] = $this['pageinfo_order'] == 'asc' ? 'desc' : 'asc';
    }

    public function addTableFieldsInput($data)
    {
        $this->tableInput = $data;
        foreach ($data as $k => $v)
        {
            $this[$k] = $v;
        }

        $pageIdentityData = MCore_Tool_Array::fetch($this, $this->pageLocatorKeys);
        $this->pageIdentityData = array_merge($pageIdentityData, $data);

        $this['page_identity_url'] = $this->getPageIdentityUrl();
    }

    protected function getPageIdentityUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = parse_url($url, PHP_URL_PATH);
        $url = MCore_Str_Url::buildUrl($this->pageIdentityData, $url);
        return $url;
    }

    public function getPageIdentityData()
    {
        return $this->pageIdentityData;
    }
}
