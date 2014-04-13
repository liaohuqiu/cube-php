<?php
/**
 *
 *
 * @author huqiu
 */
class MApps_Admin_Database_TableQuery extends MApps_AdminPageBase
{
    protected function main()
    {
        $this->getResTool()->addFootJs('admin/ATableQuery');
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table_query.html');
    }
}
