<?php
/**
 * DB
 *
 * @author huqiu
 */
class MCore_Dao_DB
{
    private $dbMan;

    public function __construct($dbMan = null)
    {
        if ($dbMan == null)
        {
            $dbMan = MCore_Proxy_DBMan::create();
        }
        $this->dbMan = $dbMan;
    }

    public function getDBMan()
    {
        return $this->dbMan;
    }

    public static function create($dbMan = null)
    {
        static $instance;
        if (!$instance)
        {
            $instance = new MCore_Dao_DB($dbMan);
        }
        return $instance;
    }

    /**
     * 插入一条数据
     *
     * @param string $kind  表名
     * @param array $dataToBeInserted 要插入的数据，对于多表，需要包含分表字段
     * @param array $fieldsWillBeChangedOnDuplicate 如果主键冲突则替换的字段
     * @param array $dataToBeAddedOnDuplucated 如果主键冲突则增加原来值，字符串拼接，数字增加
     * @param array $notEscFields 不转义的字段
     *
     * @return MCore_Dao_Result
     */
    public function insert($kind, $dataToBeInserted, $fieldsWillBeChangedOnDuplicate = array(), $dataToBeAddedOnDuplucated = array(), $notEscFields=array())
    {
        $splitId = $this->dbMan->checkInputAndGetSplitId($kind, $dataToBeInserted);

        $sql = MCore_Tool_Sql::insert($kind, $dataToBeInserted, $fieldsWillBeChangedOnDuplicate, $dataToBeAddedOnDuplucated, $notEscFields);
        return $this->dbMan->sQuery($kind, $splitId, $sql);
    }

    /**
     * 插入多条数据
     *
     * @param string $kind
     * @param array $dataListToBeInserted 要批量插入的数据，二维数组
     * @param array $fieldsWillBeChangedOnDuplicate 如果主键冲突则替换的字段
     * @param array $dataToBeAddedOnDuplucated 如果主键冲突则增加原来值，字符串拼接，数字增加
     * @param array $notEscFields 不转义的字段
     *
     * @return MCore_Dao_Result
     */
    public function insertList($kind, $dataListToBeInserted, $fieldsWillBeChangedOnDuplicate = array(), $dataToBeAddedOnDuplucated = array(), $notEscFields=array())
    {
        $tableNum = $this->dbMan->getTableNum($kind);
        if ($tableNum > 1 || $fieldsWillBeChangedOnDuplicate || $dataToBeAddedOnDuplucated || $notEscFields)
        {
            foreach ($dataListToBeInserted as $item)
            {
                $this->insert($kind, $item, $fieldsWillBeChangedOnDuplicate, $dataToBeAddedOnDuplucated, $notEscFields);
            }
        }
        else
        {
            $sql = MCore_Tool_Sql::insertList($kind, $dataListToBeInserted);
            $this->dbMan->sQuery($kind, 0, $sql);
        }
    }

    /**
     * Delete a record
     *
     * @param string $kind table kind
     * @param array $whereField The key-value condition.
     *
     * @return MCore_Dao_Result
     */
    public function delete($kind, $whereField)
    {
        $splitId = $this->dbMan->checkInputAndGetSplitId($kind, $whereField);

        $sql = MCore_Tool_Sql::delete($kind, $whereField);
        if(0 == strlen($sql))
        {
            return false;
        }
        return $this->dbMan->sQuery($kind, $splitId, $sql);
    }

    /**
     * Delete a record by a where condition
     *
     * @param $kind The table kind
     * @param int $splitId The table splitId
     * @param string $where The where condition;
     *
     * @return MCore_Dao_Result
     */
    public function deleteRawWhere($kind, $splitId, $where)
    {
        $splitField = $this->dbMan->getSplitField($kind);
        if(0 == strlen($where))
        {
            return false;
        }
        $sql = "delete from ".$kind." where ".$splitField." = '".$splitId."' and ".$where;
        return $this->dbMan->sQuery($kind, $splitId,$sql);
    }

    /**
     * 更新数据
     *
     * @param string $kind The table kind.
     * @param array $dataToBeSet Data to be set.
     * @param array $dataTobeAdded Data to be added, string will be concated, number will be added.
     *
     */
    public function update($kind, $dataToBeSet, $dataTobeAdded = array(), $whereField = array(), $notEscFields = array())
    {
        // TODO: Can't change splitId.
        $sql = MCore_Tool_Sql::update($kind, $dataToBeSet, $dataTobeAdded, $whereField, $notEscFields);
        if(0 == strlen($sql))
        {
            return false;
        }
        $splitId = $this->dbMan->checkInputAndGetSplitId($kind, $whereField);
        return $this->dbMan->sQuery($kind, $splitId, $sql);
    }

    public function updateRawWhere($kind, $splitId, $dataToBeSet, $dataTobeAdded = array(), $where, $notEscFields = array())
    {
        $splitField = $this->dbMan->getSplitField($kind);

        // add split id to $where
        $where .= (strlen(trim($where)) == 0 ? '' : ' and ') . $splitField . '=' . $splitId;

        $sql = MCore_Tool_Sql::updateRawWhere($kind, $dataToBeSet, $dataTobeAdded, $where, $notEscFields);
        if(0 == strlen($sql))
        {
            return false;
        }
        return $this->dbMan->sQuery($kind, $splitId, $sql);
    }

    public function select($kind, $selectField, $whereField = array(), $order = "", $start=0, $num=0, $cacheTime=0)
    {
        $splitId = $this->dbMan->checkInputAndGetSplitId($kind, $whereField);
        $sql = MCore_Tool_Sql::select($kind, $selectField, $whereField, $order, $start, $num);
        return $this->dbMan->sQuery($kind, $splitId,$sql, $cacheTime);
    }

    public function selectRawWhere($kind, $splitId, $selectField, $where = "", $order = "", $start=0, $num=0, $cacheTime=0)
    {
        $tableNum = $this->dbMan->getTableNum($kind);
        if ($tableNum > 1)
        {
            $splitField = $this->dbMan->getSplitField($kind);
            $where .= ($where == '') ? '' : ' and ';
            $where .= $splitField . "='" . MCore_Tool_Sql::escape_string($splitId) . "'";
        }

        $sql = MCore_Tool_Sql::selectRawWhere($kind, $selectField, $where, $order, $start, $num);
        return $this->dbMan->sQuery($kind, $splitId,$sql, $cacheTime);
    }

    public function selectEx($kind, $selectField, $whereField, $order, $start, $num, $cacheTime=0)
    {
        $splitId = $this->dbMan->checkInputAndGetSplitId($kind, $whereField);
        $sql = array();
        $sql[0] = MCore_Tool_Sql::select($kind, $selectField, $whereField, $order, $start, $num, true);
        $sql[1] = MCore_Tool_Sql::foundRows();

        $dbDataRaw = $this->dbMan->mQuery($kind, $splitId,$sql, $cacheTime);
        return MCore_Dao_Result::foundRowsData($dbDataRaw);
    }

    public function selectExRawWhere($kind, $splitId, $selectField, $where, $order, $start, $num, $cacheTime=0)
    {
        $tableNum = $this->dbMan->getTableNum($kind);
        if ($tableNum > 1)
        {
            $splitField = $this->dbMan->getSplitField($kind);
            $where .= ($where == "") ? "":" and ";
            $where .= $splitField . "='" . MCore_Tool_Sql::escape_string($splitId) . "'";
        }

        $sql = array();
        $sql[0] = MCore_Tool_Sql::selectRawWhere($kind, $selectField, $where, $order, $start, $num, true);
        $sql[1] = MCore_Tool_Sql::foundRows();

        $dbDataRaw = $this->dbMan->mQuery($kind, $splitId, $sql, $cacheTime);
        return MCore_Dao_Result::foundRowsData($dbDataRaw);
    }
}
