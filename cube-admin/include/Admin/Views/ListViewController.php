<?php
/**
 * render a table view quickly.
 *
 * @author http://www.liaohuqiu.net
 */
class MAdmin_Views_ListViewController
{
    // config array
    private $conf;

    // to access data table
    private $dataTable;

    // input parameters
    private $input;

    // for rendering the quick query header
    private $pageQuickQuery;

    // table header information
    private $listHeader;

    // data engine
    private $dataOne;

    // the callback for formatting data item
    private $formatDataItem;

    // the callback for formating every item in table cell
    private $formatDisplayDataItem;

    public function __construct($conf, $dataOne)
    {
        $this->conf = $conf;
        $this->listHeader = new MAdmin_Views_ListHeader($conf['header']);
        $this->dataTable = new MAdmin_Views_ListDataTable($conf['table'], $dataOne);

        $this->pageQuickQuery = new MAdmin_Views_ListQuickQuery($conf['quickQuery']);
        $this->pageQuickQuery->addSearchInfo($conf['table']['search']);

        $this->formatDataItem = $conf['format_data_item'];
        $this->formatDisplayDataItem = $conf['format_display_data_item'];
    }

    public function render()
    {
        $this->input = new MAdmin_Views_ListPageInput();
        $this->input->addTableFieldsInput($this->dataTable->getFieldsInput());

        $tableData = $this->dataTable->queryData($this->input);
        $tableHeaderData = $this->listHeader->getHeaderData($this->input, $this->dataTable->getTableFields());

        if ($this->actionName == 'download')
        {
            $tableHeaderNames = MCore_Tool_Array::getFields($tableHeaderData, 'name');
            $this->_outputData($tableHeaderNames, $tableData['list']);
        }
        else
        {
            $data = array();
            $data['thead'] = $tableHeaderData;
            $data['pagination'] = $this->_getPaginationData($tableData['total']);
            $data['rowList'] = $this->_processRowList($tableData['list'], $tableHeaderData);
            $data['url_create_new'] = $this->_buildCreateNewUrl();
            $data['conf'] = $this->conf;

            $view = MApps_AdminPageBase::createDisplayView();
            $view->setPageData($data);
            $view->display('admin/widget/list.html');
        }
    }

    private function _buildCreateNewUrl()
    {
        if (!$this->conf['edit_info']['can_create'])
        {
            return '';
        }
        $url = $this->conf['edit_info']['edit_url'];
        if ($url)
        {
            $qureyInfo = array();
            $qureyInfo['refer'] = $this->input['page_identity_url'];
            $qureyInfo['table_kind'] = $this->dataTable->getKind();
            $url = MCore_Str_Url::buildUrl($qureyInfo, $url);
        }
        return $url;
    }

    protected function buildActionUrlForListItem($primaryKeys, $dataItem, $action)
    {
        $qureyInfo = MCore_Tool_Array::fetch($dataItem, $primaryKeys);
        $qureyInfo['edit_action_name'] = $action;
        $qureyInfo['refer'] = $this->input['page_identity_url'];
        $qureyInfo['table_kind'] = $this->dataTable->getKind();
        return MCore_Str_Url::buildUrl($qureyInfo, $this->conf['edit_info']['edit_url']);
    }

    private function _formatDisplayItem($item)
    {
        if ($this->formatDisplayDataItem)
        {
            $item = call_user_func($this->formatDisplayDataItem, $item);
        }
        return $item;
    }

    private function _formatDataItem($item)
    {
        if ($this->formatDataItem)
        {
            $item = call_user_func($this->formatDataItem, $item);
        }
        return $item;
    }

    protected function _processRowList($list, $tableHeaderData)
    {
        $rowList = array();
        $subTableInfo = $this->conf['subTableInfo'];

        $primaryKeys = $this->dataTable->getPrimaryKeys();
        foreach ($list as $dataItem)
        {
            $dataItem = $this->_formatDataItem($dataItem);

            $row = array();
            if ($this->conf['edit_info']['edit_url'])
            {
                $row['url_edit_info'] = $this->buildActionUrlForListItem($primaryKeys, $dataItem, 'edit');
                $row['url_delete_info'] = $this->buildActionUrlForListItem($primaryKeys, $dataItem, 'delete');
            }

            // 子列表信息
            if (!empty($subTableInfo))
            {
                $subTableQueryInfo = array();
                $subTableQueryInfo['table_kind'] = $subTableInfo['table_kind'];
                foreach($subTableInfo['keyMap'] as $key => $outKey)
                {
                    $subTableQueryInfo[$outKey] = $dataItem[$key];
                }
                $row['subTableLink'] = MCore_Str_Url::buildUrl($subTableQueryInfo,$subTableInfo['url']);
            }
            $row['dataItem'] = $this->_formatDisplayItem($dataItem);
            $rowList[] = $row;
        }
        return $rowList;
    }

    private function _getPaginationData($total)
    {
        $num_perpage = $this->input['pageinfo_num_perpage'];
        $data = $this->input->getPageIdentityData();
        $this->pagination = new MAdmin_Views_ListPagination($num_perpage, $data);
        $this->pagination->setStart($this->input['start']);

        $data = $this->pagination->getPaginationData();
        $list = array(20 => 20, 50 => 50, 100 => 100, 300 => 300, 500 => 500, 1000 => 1000, 2000 => 2000, 10000 => 10000, 100000 => 100000);
        $data['numPerPageOptions'] = MCore_Str_Html::options($list, $num_perpage);

        return $data;
    }

    protected function getDownLoadFileName()
    {
        $fielName = $this->table_kind;
        foreach($this->whereInfo as $key => $value)
        {
            $fielName .= '_' . $key .'_' . $value;
        }
        return $fileName;
    }

    private function _outputData($fileName, $tableHeaderNames, $srcList)
    {
        $dataList = array();
        $dataList[] = array_values($tableHeaderNames);
        foreach ($srcList as $dataItem)
        {
            $info = array();
            foreach ($tableHeaderNames as $key => $name)
            {
                if(isset($dataItem[$key]))
                {
                    $info[$key] = $dataItem[$key];
                }
                else
                {
                    $info[$key] = '';
                }
            }
            $dataList[] = $info;
        }

        $csvContent = MCore_Tool_Csv::fromArray($dataList);
        header('Content-type:application/vnd.ms-excel');
        header('content-Disposition:filename=$fielName.csv');
        echo $csvContent;
        exit();
    }
}
?>