<?php
/**
 *  翻页
 *
 * @author      huqiu
 */
class MAdmin_Views_ListPagination
{
    private $_total;
    private $_totalPage;

    private $_currentStart = 0;
    private $_currentPage;

    private $_numPerPage;
    private $_otherUrlKVInfo;
    private $_url;

    function __construct($numPerPage, $otherUrlKVInfo = array(), $url = '')
    {
        $this->_numPerPage = $numPerPage;
        $this->_otherUrlKVInfo = $otherUrlKVInfo;
        $this->_url = $url;
    }

    /**
     * @param mixed $currentStart      当前开始
     * @param mixed $numPerPage        每页项数
     * @param mixed $otherUrlKVInfo    其他要附带如url的信息
     * @param mixed $url               url
     */
    public static function create($numPerPage, $otherUrlKVInfo = array(), $url = '')
    {
        return new MAdmin_Views_ListPagination($numPerPage, $otherUrlKVInfo, $url);
    }

    public function setStart($start)
    {
        if ($start > 0)
        {
            $this->_currentStart = $start;
        }
        return $this;
    }

    public function setTotal($total)
    {
        $this->_total = $total;
        return $this;
    }

    public function setInfo($info)
    {
        $this->_otherUrlKVInfo = $info;
        return $this;
    }

    public function getStart()
    {
        return $this->_currentStart;
    }

    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    public function getPaginationData()
    {
        $this->_currentPage = floor($this->_currentStart / $this->_numPerPage) + 1;
        $this->_totalPage = ceil($this->_total / $this->_numPerPage);

        //无数据时候
        if (!$this->_total || $this->_totalPage == 1)
        {
            return "";
        }

        $pageInfo = array();

        $pageInfo['totalPage'] = $this->_totalPage;
        $pageInfo['currentPage'] = $this->_currentPage;

        $pageInfo["head_url"] = $this->_getHeadUrl();
        $pageInfo["tail_url"] = $this->_getTailUrl();

        $pageInfo["prev_url"] = $this->_getPrevPageUrl();
        $pageInfo['next_url'] = $this->_getNextPageUrl();

        $pageInfo["pages"] = $this->_getPages();

        $pageInfo['start'] = $this->_currentStart;
        $pageInfo['total'] = $this->_total;
        return $pageInfo;
    }

    private function _getPages()
    {
        if ($this->_totalPage <= 5)
        {
            $start = 1;
            $end = $this->_totalPage;
        }
        else
        {
            if ($this->_currentPage >= 3)
            {
                $remainPageNum = $this->_totalPage - $this->_currentPage;
                if ($remainPageNum <= 2)
                {
                    $start = $this->_totalPage - 5 + 1;
                    $end = $this->_totalPage;
                }
                else
                {
                    $start = $this->_currentPage - 2;
                    $end = $this->_currentPage + 2;
                }
            }
            else
            {
                $start = 1;
                $end = 5;
            }
        }

        //确保不超范围
        $end > $this->_totalPage && $end = $this->_totalPage;
        $start < 1 && $start = 1;

        $pages = array();
        for ($i = $start; $i <= $end; $i++)
        {
            $pageInfo = array();
            $pageInfo["page"] = $i;
            $pageStart = ($i -1) * $this->_numPerPage;
            $pageInfo["url"] = $this->_buildUrl(array("page_start" => $pageStart));
            $pageInfo["is_current"] = $this->_currentPage == $i;
            $pages[] = $pageInfo;
        }
        return $pages;
    }

    private function _isAtFirstPage()
    {
        return $this->_currentPage == 1;
    }

    private function _isAtLastPage()
    {
        return $this->_currentPage == $this->_totalPage;
    }

    private function _getHeadUrl()
    {
        if ($this->_isAtFirstPage())
        {
            return "";
        }

        $info = array("page_start" => 0);
        return $this->_buildUrl($info);
    }

    private function _getTailUrl()
    {
        if ($this->_isAtLastPage())
        {
            return "";
        }
        $pageStart = ($this->_totalPage - 1) * $this->_numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->_buildUrl($info);
    }

    private function _getPrevPageUrl()
    {
        if ($this->_isAtFirstPage())
        {
            return "";
        }

        $pageStart = ($this->_currentPage - 2) * $this->_numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->_buildUrl($info);
    }

    private function _getNextPageUrl()
    {
        if ($this->_isAtLastPage())
        {
            return "";
        }
        $pageStart = $this->_currentPage * $this->_numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->_buildUrl($info);
    }

    private function _buildUrl($info)
    {
        $info = array_merge($this->_otherUrlKVInfo, $info);
        $link = $this->_url . '?' . http_build_query($info);
        return $link;
    }
}
