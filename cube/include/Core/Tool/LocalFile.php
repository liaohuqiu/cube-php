<?php
/**
 *
 * @author huqiu
 */
class MCore_Tool_LocalFile
{
    /**
     *   $info = array();
     *   $info['file_name'] = $uploadInfo['name'];
     *   $info['ext_name'] = $fileType;
     *   $info['ext_code'] = $extCode;
     *   $info['file_path'] = $filePath;
     *   $info['store_path'] = $storePath;
     */
    public static function saveUploadFile($uploadInfo, $baseDir = '', $fileType = '')
    {
        if (!$uploadInfo || $uploadInfo['error'])
        {
            return false;
        }

        $srcFilePath = $uploadInfo['tmp_name'];

        $extCode = 0;
        !$fileType && $fileType = self::getFileType($srcFilePath, $extCode);
        $fileName = md5_file($srcFilePath) . '.' . $fileType;

        empty($baseDir) && $baseDir = self::getUploadBaseDir();
        $storeDir = self::getLocalPath($fileName);
        $storePath = $storeDir . '/' . $fileName;
        $filePath = $baseDir . $storePath;

        $dir = dirname($filePath);
        if (!is_dir($dir))
        {
            mkdir($dir, 0777, true);
        }

        if (!file_exists($filePath))
        {
            move_uploaded_file($srcFilePath, $filePath);
        }

        $info = array();
        $info['file_name'] = $uploadInfo['name'];
        $info['ext_name'] = $fileType;
        $info['ext_code'] = $extCode;
        $info['file_path'] = $filePath;
        $info['store_path'] = $storePath;

        return $info;
    }

    public static function getUploadBaseDir()
    {
        return ROOT_DIR . '/htdocs';
    }

    public static function getFileType($filePath, &$extCode = 0)
    {
        $file = fopen($filePath, "rb");
        $bin = fread($file, 2);
        fclose($file);
        $strInfo = @unpack("C2chars", $bin);
        $extCode = intval($strInfo['chars1'] . $strInfo['chars2']);
        switch ($extCode)
        {
        case 3533:
            $fileType = 'amr';
            break;
        case 7790:
            $fileType = 'exe';
            break;
        case 7784:
            $fileType = 'midi';
            break;
        case 8297:
            $fileType = 'rar';
            break;
        case 255216:
            $fileType = 'jpg';
            break;
        case 7173:
            $fileType = 'gif';
            break;
        case 6677:
            $fileType = 'bmp';
            break;
        case 13780:
            $fileType = 'png';
            break;
        case 7368:
            $fileType = 'mp3';
            break;
        case 4838:
            $fileType = 'wma';
            break;
        case 9997:
            $fileType = 'aac';
            break;
        case 109111:
            $fileType = 'aac';
            break;
        case 208207:
            $fileType = 'xls';
            break;
        case 6787:
            $fileType = 'swf';
            break;
        default:
            $fileType = false;
        }
        return $fileType;
    }

    public static function getLocalPath($fileName)
    {
        $fileId = abs(crc32($fileName));
        $path = '/file/';
        $path .= (floor($fileId / 1000000) % 100) . '/';
        $path .= (floor($fileId / 10000) % 100) . '/';
        $path .= (floor($fileId / 100) % 100);
        return $path;
    }
}
