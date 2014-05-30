<?php
/**
 *   qmail
 *
 * @author      huqiu
 */
class MCore_Util_QMail
{
    private $_from = '';
    private $_fromName = '';
    private $_returnPath = '';
    private $_title = '';
    private $_contentt = array();
    private $_sigText = '';
    private $_cc = array();
    private $_receiver = array();
    private $_attachmentList = array();

    function __construct($title, $from, $fromName)
    {
        $this->_title = $title;
        $this->_fromName = $fromName;
        $this->_from = $from;
    }

    function addSig($sigText)
    {
        $this->_sigText = $sigText;
        return $this;
    }

    function setContent($content)
    {
        $this->_content = $content;
        return $this;
    }

    function addAttachment($fileOrFileList)
    {
        $this->_attachmentList = $this->_mergeList($fileOrFileList,$this->_attachmentList);
        return $this;
    }

    private function _mergeList($itemOrList,$oldList)
    {
        $newList = array();
        if(!is_array($itemOrList))
        {
            $newList = array($itemOrList);
        }
        else
        {
            $newList = $itemOrList;
        }
        $newList = array_merge($oldList,$newList);
        return array_unique($newList);
    }

    private function _checkEmail($email)
    {
        if(!MCore_Str_Check::checkEmail($email))
        {
            throw new Exception('email address is illegal');
        }
    }

    public function send($receiver, $cc = array())
    {
        $this->_receiver = (array)$receiver;
        $this->_cc = (array)$cc;

        $this->_checkPara();

        //邮件信息
        $mailInfo = array();
        $mailInfo['returnPath'] = $this->_returnPath;
        $mailInfo['fromName'] = base64_encode($this->_fromName);
        $mailInfo['from'] = $this->_from;
        $mailInfo['boundary'] = '-----mixed-' .md5(date('r', time()));
        $mailInfo['subject'] = base64_encode($this->_title);
        $mailInfo['to'] = $this->_intoList($this->_receiver);
        $mailInfo['cc'] = $this->_intoList($this->_cc);
        $mailInfo['charset'] = 'utf-8';
        $mailInfo['bodyList'] = $this->_getBodyList();
        $mailInfo['attachmentList'] = $this->_getAttachmentList();

        $path = KXM_ROOT_DIR . '/data/template';
        $smarty = new MCore_Tool_Smarty($path);
        $smarty->assignRaw('mailInfo',$mailInfo);
        $mailData = $smarty->fetch('mail.html');

        $handle = popen('/var/qmail/bin/qmail-inject', 'w');
        fwrite($handle,$mailData);
        pclose($handle);

        return $mailData;
    }

    private function _intoList($list)
    {
        if($list)
        {
            return '<' . implode('>,<',$list) . '>';
        }
        return '';
    }

    private function _getBodyList()
    {
        $bodyList = array();
        if($this->_sigText)
        {
            $this->_content = $this->_content . "\n" .$this->_sigText;
        }

        $info = array();
        $info['type'] = 'text/html';
        $info['content'] = chunk_split(base64_encode($this->_content));
        $bodyList[] = $info;

        return $bodyList;
    }

    private function _getAttachmentList()
    {
        $list = array();

        foreach($this->_attachmentList as $file)
        {
            if(!file_exists($file))
            {
                throw new Exception("this file $file is not exist.");
            }

            $info = array();
            $info['name'] = basename($file);
            $info['filename'] = basename($file);
            $info['content'] = chunk_split(base64_encode(file_get_contents($file)));
            $list[] = $info;
        }
        return $list;
    }

    private function _checkPara()
    {
        !is_array($this->_receiver) && $this->_receiver = array($this->_receiver);
        !$this->_receiver && $this->_error('receiver is empty');
        !is_array($this->_cc) && $this->_cc = array($this->_cc);

        foreach(array_merge($this->_receiver,$this->_cc) as $email)
        {
            $this->_checkEmail($email);
        }

        $this->_checkEmail($this->_from);

        !$this->_returnPath && $this->_returnPath = $this->_from;

        !$this->_title && $this->_title = 'No title';
        !$this->_fromName && $this->_fromName = $this->_from;
    }

    private function _error($msg)
    {
        throw new Exception($msg);
    }
}
