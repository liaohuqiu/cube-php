<?php
class MApps_Init_InitIndex extends MApps_AdminPageBase
{
    protected function checkAuth()
    {
    }

    protected function main()
    {
        $data = array();
        $data['error_msg'] = $this->checkIfHasError();
        $data['warning_msg'] = $this->checkIfHasWarning();
        $data['sys_config_path'] = 'aaaa';
        $data['sys_config_path'] = MEngine_SysConfig::getSysConfigPath();
        $data['deploy_data_path'] = MCore_Min_TableConfig::getConfigPath();
        $this->getView()->setPageData($data);
        $this->getResTool()->addFootJs('admin/AInit.js');
    }

    protected function checkIfHasWarning()
    {
        $path = CONFIG_DATA_DIR;
        if (!is_writable($path))
        {
            return 'CONFIG_DATA_DIR is not writeable, resource(js/css) auto generation will be disabled:  <code>' . CONFIG_DATA_DIR . '</code>';
        }
    }

    protected function checkIfHasError()
    {
        return false;
    }

    protected function outputBody()
    {
        $this->getView()->display('init/index.html');
    }
}
