<?php
class MAdmin_Views_ListDataTable
{
    private $kind;
    private $defaultOrderBy;
    private $searchFields = array();

    private $tableFields;
    private $primaryKeys;

    private $fieldsData;
    private $selectField = array('*');
    private $dataOne;

    public function __construct($conf, $dataOne)
    {
        if (!isset($conf['kind']))
        {
            throw new Exception('Not kind field in $conf');
        }

        $this->dataOne = $dataOne;
        $this->kind = $conf['kind'];
        $this->defaultOrderBy = $conf['default_order_by'];

        if ($conf['search']['fields'])
        {
            $this->searchFields = $conf['search']['fields'];
        }

        if ($conf['search']['select_field'])
        {
            $this->selectField = $conf['search']['select_field'];
        }

        $dbMan = $dataOne->getDBMan();
        $tableSchema = MCore_Min_TableSchema::create($this->kind)->query($dbMan);

        //主键
        $this->primaryKeys = $tableSchema->getPriKeys();
        //所有键
        $this->tableFields = $tableSchema->getKeys();
    }

    public function getTableFields()
    {
        return $this->tableFields;
    }

    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    /**
     * fetch the input value for the table fields;
     */
    public function getFieldsInput()
    {
        if (!$this->fieldsData)
        {
            $info = array();
            foreach($this->tableFields as $fieldKey)
            {
                if(isset($_REQUEST[$fieldKey]))
                {
                    $fieldValue = MCore_Tool_Input::clean('r', $fieldKey, 'str');
                    $info[$fieldKey] = $fieldValue;
                }
            }
            $this->fieldsData = $info;
        }
        return $this->fieldsData;
    }

    public function queryData($input)
    {
        $order = $this->getOrderSql($input);
        $where = $this->getWhereSql($input);
        $start = $input['pageinfo_start'];
        $num_perpage = $input['pageinfo_num_perpage'];
        $ret = $this->dataOne->selectExRawWhere($this->kind, 0, $this->selectField, $where, $order, $start, $num_perpage);

        $data = array();
        $data['list'] = $ret->toArray();
        $data['total'] = $ret['total'];
        return $data;
    }

    protected function getWhereSql($input)
    {
        $where = MCore_Tool_Sql::where($this->getFieldsInput());

        $searchValue = $input['search_value'];
        if ($searchValue)
        {
            $strs = array();
            foreach ($this->searchFields as $field)
            {
                $strs[] = "$field like '%" . MCore_Tool_Sql::escape_string($searchValue) . "%'";
            }

            if ($strs)
            {
                $searchWhere = implode(' or ', $strs);
                if ($where)
                {
                    $where = $where . " and ($searchWhere)";
                }
                else
                {
                    $where = $searchWhere;
                }
            }
        }
        return $where;
    }

    public function getKind()
    {
        return $this->kind;
    }

    protected function getOrderSql($input)
    {
        $orderBy = $input['pageinfo_sortby'];
        $order = $input['pageinfo_order'];
        $str = '';
        if ($order && $orderBy)
        {
            $str = "order by $orderBy $order";
        }
        else
        {
            $str = $this->_conf['default_order'];
        }
        return $str;
    }
}
