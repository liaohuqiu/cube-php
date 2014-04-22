<?php
/**
 * DB 中间件抽象
 */
interface MCore_Proxy_IDBMan
{
    /**
     * 查询一条sql
     */
    public function sQuery($kind, $hintId, $sql, $cacheTime = 0);

    /**
     * 多条sql
     */
    public function mQuery($kind, $hintId, $sqls, $cacheTime = 0);

    /**
     * 检查输入，并获取分表id
     */
    public function checkInputAndGetSplitId($kind, $input);

    /**
     * 获取表数量
     */
    public function getTableNum($kind);

    /**
     * 获取分表字段
     */
    public function getSplitField($kind);
}
