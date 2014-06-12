<?php
class MApps_Sample_UploadAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        $file = $this->getRequest()->getData('f1', 'f', 'file');
        if (is_array($file) && !$file['error'])
        {
            $fileName = $file['name'];
            $filePath = $file['tmp_name'];
        }
        else
        {
            $httpKey = 'HTTP_CONTENT_DISPOSITION';
            $ptn = '/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i';
            if (isset($_SERVER[$httpKey]) && preg_match($ptn, $_SERVER[$httpKey], $info))
            {
                $fileName = urldecode($info[2]);
                $filePath = 'php://input';
            }
        }

        $ext1 = pathinfo($fileName, PATHINFO_EXTENSION);
        $ext2 = MCore_Tool_LocalFile::getFileType($filePath);

        $data = array();
        $data['filename'] = $fileName;
        $data['ext1'] = $ext1;
        $data['ext2'] = $ext2;
        $this->setData($data);
    }
}
