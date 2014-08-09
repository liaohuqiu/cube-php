<?php
class MApps_Admin_Database_TableList extends MApps_AdminPageBase
{
    protected function main()
    {
        $this->getResTool()->addFootJs('admin/ATableList');
    }

    protected function outputBody()
    {
        $table = array(
            'kind' => 'sys_table_info',
            'select_field' => array(),
            'default_order_by' => 'order by kind asc',
            'search' => array(
                'fields' => array('kind'),
                'desc' => 'kind',
            ),
        );

        $header = array(
            'no_sort_filds' => array(),
            'this_value_fields' => array(),
            'hide_fields' => array('id'),
            'only_display_fields' => array(),
            'names' => array(
                'delete_table' => 'delete',
                'alter_table' => 'alter',
            ),
            'align' => array(
                'name' => 'left',
            ),
        );

        $conf = array(
            'download' => '1',
            'subTableInfo' => array(
                'kind'=>'',
                'keyMap' => array('key'=>'outKey'),
                'url'=>'url',
            ),
            'pageQuery' => array(),
        );

        $conf['table'] = $table;
        $conf['header'] = $header;
        $conf['format_display_data_item'] = array($this, 'formatDisplayItem');
        $c = new MAdmin_Views_ListViewController($conf, MEngine_EngineDB::fromConfig());
        $c->render();
    }

    public function formatDisplayItem($item)
    {
        $kind = $item['name'];
        $item['delete_table'] = "<a class='_j_table_delete' href='javascript:void(0)' data-kind='$kind'>delete</a>";
        $item['alter_table'] = "<a href='table-edit?kind=$kind' target='_blank' >alter</a>";
        return $item;
    }
}
