<?php
/**
 *  list pagination
 *
 * @author     liaohuqiu@gmail.com
 */
class MAdmin_Views_ListPagination
{
    private $total;
    private $totalPage;

    private $currentStart = 0;
    private $currentPage;

    private $numPerPage;
    private $otherUrlKVInfo;
    private $url;

    /**
     * @param mixed $numPerPage        每页项数
     * @param mixed $otherUrlKVInfo    其他要附带如url的信息
     * @param mixed $url               url
     */
    public static function create($numPerPage, $otherUrlKVInfo = array(), $url = '')
    {
        return new MAdmin_Views_ListPagination($numPerPage, $otherUrlKVInfo, $url);
    }

    public function __construct($numPerPage, $otherUrlKVInfo = array(), $url = '')
    {
        $this->numPerPage = $numPerPage;
        $this->otherUrlKVInfo = $otherUrlKVInfo;
        $this->url = $url;
    }

    public function setStart($start)
    {
        if ($start > 0)
        {
            $this->currentStart = $start;
        }
        return $this;
    }

    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    public function setInfo($info)
    {
        $this->otherUrlKVInfo = $info;
        return $this;
    }

    public function getStart()
    {
        return $this->currentStart;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getPaginationData()
    {
        if (!$this->numPerPage)
        {
            throw new Exception('You should set numPerPage first');
        }
        $this->currentPage = floor($this->currentStart / $this->numPerPage) + 1;
        $this->totalPage = ceil($this->total / $this->numPerPage);

        $pageInfo = array();

        $pageInfo['total_page'] = $this->totalPage;
        $pageInfo['current_page'] = $this->currentPage;

        $pageInfo["head_url"] = $this->getHeadUrl();
        $pageInfo["tail_url"] = $this->getTailUrl();

        $pageInfo["prev_url"] = $this->getPrevPageUrl();
        $pageInfo['next_url'] = $this->getNextPageUrl();

        $pageInfo["pages"] = $this->getPages();

        $pageInfo['start'] = $this->currentStart;
        $pageInfo['total'] = $this->total;
        return $pageInfo;
    }

    private function getPages()
    {
        if ($this->totalPage <= 5)
        {
            $start = 1;
            $end = $this->totalPage;
        }
        else
        {
            if ($this->currentPage >= 3)
            {
                $remainPageNum = $this->totalPage - $this->currentPage;
                if ($remainPageNum <= 2)
                {
                    $start = $this->totalPage - 5 + 1;
                    $end = $this->totalPage;
                }
                else
                {
                    $start = $this->currentPage - 2;
                    $end = $this->currentPage + 2;
                }
            }
            else
            {
                $start = 1;
                $end = 5;
            }
        }

        //确保不超范围
        $end > $this->totalPage && $end = $this->totalPage;
        $start < 1 && $start = 1;

        $pages = array();
        for ($i = $start; $i <= $end; $i++)
        {
            $pageInfo = array();
            $pageInfo["page"] = $i;
            $pageStart = ($i -1) * $this->numPerPage;
            $pageInfo["url"] = $this->buildUrl(array("page_start" => $pageStart));
            $pageInfo["is_current"] = $this->currentPage == $i;
            $pages[] = $pageInfo;
        }
        return $pages;
    }

    private function isAtFirstPage()
    {
        return $this->currentPage == 1;
    }

    private function isAtLastPage()
    {
        return $this->currentPage == $this->totalPage;
    }

    private function getHeadUrl()
    {
        if ($this->isAtFirstPage())
        {
            return "";
        }

        $info = array("page_start" => 0);
        return $this->buildUrl($info);
    }

    private function getTailUrl()
    {
        if ($this->isAtLastPage())
        {
            return "";
        }
        $pageStart = ($this->totalPage - 1) * $this->numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function getPrevPageUrl()
    {
        if ($this->isAtFirstPage())
        {
            return "";
        }

        $pageStart = ($this->currentPage - 2) * $this->numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function getNextPageUrl()
    {
        if ($this->isAtLastPage())
        {
            return "";
        }
        $pageStart = $this->currentPage * $this->numPerPage;
        $info = array("page_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function buildUrl($info)
    {
        $info = array_merge($this->otherUrlKVInfo, $info);
        $link = $this->url . '?' . http_build_query($info);
        return $link;
    }
}
