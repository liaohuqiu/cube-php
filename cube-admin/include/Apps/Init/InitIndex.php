<?php
class MApps_Init_InitIndex extends MApps_AdminPageBase
{
    protected function checkAuth()
    {
    }

    protected function main()
    {
        $data = array();
        $hasInit = MAdmin_Init::checkInit();
        $data['has_init'] = $hasInit;
        if ($hasInit)
        {
            $data['ok_msg'] = 'The cube has been installed.';
        }
        else
        {
            $data['ok_msg'] = "It's ready, you can deploy now.";
        }
        $data['error_msg'] = $this->checkIfHasError();
        $data['warning_msg'] = $this->checkIfHasWarning();
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
            return '<code>CONFIG_DATA_DIR</code> is not writeable, resource(js/css) auto generation will be disabled:  <code>' . $path . '</code>';
        }
    }

    protected function checkIfHasError()
    {
        $path = WRITABLE_DIR;
        if (!is_writable($path) && !MCore_Tool_Env::isProd())
        {
            return 'The <code>WRITABLE_DIR</code> can not be written:  <code>' . $path . '</code>';

        }
        return false;
    }

    protected function outputBody()
    {
        $this->getView()->display('init/index.html');
    }
}
