<?php
/**
 * A common page to opertate the data in split table
 *
 * @author http://www.liaohuqiu.net
 */
class MAdmin_Views_DataTableEditController extends MAdmin_Views_EditController implements MAdmin_Views_EditDataProvider
{
    private $tableKind;
    private $primaryKeyList;
    private $tableFields;

    private $db;

    public function __construct($conf = array(), $db)
    {
        $this->db = $db;
        $conf = $this->prepareConf($conf);
        $this->setDataProvider($this);
        parent::__construct($conf);
    }

    protected function prepareConf($conf)
    {
        $this->tableKind = MCore_Tool_Input::clean('r', 'table_kind', 'str');

        // query the database scheme information
        $dbMan = $this->db->getDBMan();
        $tableSchema = MCore_Min_TableSchema::create($this->tableKind)->query($dbMan);

        $this->tableFields = $tableSchema->getKeys();
        $tableInfo = $tableSchema->getInfo();
        $primaryKeys = $tableSchema->getPriKeys();

        $editInfo = $conf['edit_info'];
        !$editInfo && $editInfo = array();
        $keys = array_unique(array_merge($this->tableFields, array_keys($editInfo)));

        $itemList = array();
        foreach ($keys as $key)
        {
            if (isset($editInfo[$key]))
            {
                $info = $editInfo[$key];
            }
            else
            {
                $fieldInfo = $tableInfo[$key];

                $info = array();
                $info['name'] = $key;
                $info['title'] = $key;
                if (!isset($info["edit"]))
                {
                    $info['lock'] = in_array($key, $primaryKeys);
                }
                $info['value'] = $fieldInfo['Default'];
            }
            $itemList[$key] = $info;
        }

        $conf = array();
        $conf['identity_keys'] = $primaryKeys;
        $conf['edit_info'] = $itemList;
        return $conf;
    }

    public function getInfo($identityInfo)
    {
        $ret = $this->db->select($this->tableKind, array("*"), $identityInfo);
        return $ret["data"][0];
    }

    public function submit($setArr, $identityInfo)
    {
        $now = MCore_Util_DateTime::now()->format();
        if (!isset($setArr["ctime"]) && in_array('ctime', $this->tableFields))
        {
            $setArr["ctime"] = $now;
        }
        if (!isset($setArr["mtime"]) && in_array('mtime', $this->tableFields))
        {
            $setArr["mtime"] = $now;
        }
        $dbArr = array_merge($setArr,$identityInfo);
        $this->db->insert($this->tableKind, $dbArr,array_keys($setArr));
    }

    public function delete($identityInfo)
    {
        $this->db->delete($this->tableKind, $identityInfo);
    }
}
