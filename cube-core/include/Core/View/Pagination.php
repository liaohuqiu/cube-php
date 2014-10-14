<?php
/**
 *  list pagination
 *
 * @author     liaohuqiu@gmail.com
 */
class MCore_View_Pagination
{
    private $total;
    private $totalPage;

    private $currentStart = 0;
    private $currentPage;

    private $numPerPage;
    private $otherUrlKVInfo;
    private $url;

    /**
     * @param mixed $numPerPage
     * @param mixed $otherUrlKVInfo
     * @param mixed $url
     */
    public static function create($numPerPage, $otherUrlKVInfo = array(), $url = '')
    {
        return new MCore_View_Pagination($numPerPage, $otherUrlKVInfo, $url);
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
            $pageInfo["url"] = $this->buildUrl(array("pageinfo_start" => $pageStart));
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

        $info = array("pageinfo_start" => 0);
        return $this->buildUrl($info);
    }

    private function getTailUrl()
    {
        if ($this->isAtLastPage())
        {
            return "";
        }
        $pageStart = ($this->totalPage - 1) * $this->numPerPage;
        $info = array("pageinfo_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function getPrevPageUrl()
    {
        if ($this->isAtFirstPage())
        {
            return "";
        }

        $pageStart = ($this->currentPage - 2) * $this->numPerPage;
        $info = array("pageinfo_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function getNextPageUrl()
    {
        if ($this->isAtLastPage())
        {
            return "";
        }
        $pageStart = $this->currentPage * $this->numPerPage;
        $info = array("pageinfo_start" => $pageStart);
        return $this->buildUrl($info);
    }

    private function buildUrl($info)
    {
        $info = array_merge($this->otherUrlKVInfo, $info);
        $link = $this->url . '?' . http_build_query($info);
        return $link;
    }
}
/*
$pagination = new MCore_View_Pagination($num_perpage, $data);
$pagination->setStart($this->input['pageinfo_start'])->setTotal($total);
$data = $pagination->getPaginationData();

$list = array(20 => 20, 50 => 50, 100 => 100);
$data['num_per_page_options'] = MCore_Str_Html::options($list, $num_perpage);

$data = array (
    'total_page' => 2,
    'current_page' => 1,
    'head_url' => '',
    'tail_url' => '?pageinfo_start=20',
    'prev_url' => '',
    'next_url' => '?pageinfo_start=20',
    'pages' =>
    array (
        0 =>
        array (
            'page' => 1,
            'url' => '?pageinfo_start=0',
            'is_current' => true,
        ),
        1 =>
        array (
            'page' => 2,
            'url' => '?pageinfo_start=20',
            'is_current' => false,
        ),
    ),
    'start' => 0,
    'total' => 29,
    'num_per_page_options' => '<option value=\'20\' selected = \'true\'>20</option><option value=\'50\'>50</option><option value=\'100\'>100</option>',
)
 */
