<?php
class MApps_Admin_Database_TableConfig extends MApps_AdminPageBase
{
    protected function main()
    {
        $data = array();
        $data['deploy_data_str'] = MCore_Tool_Conf::formatConfigData(MEngine_MysqlDeploy::getDeployData());
        $data['deploy_data_path'] = MCore_Min_TableConfig::getConfigPath();
        $this->getView()->setPageData($data);
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table-config.html');
    }
}
