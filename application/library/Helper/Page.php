<?php
/**
 * Created by PhpStorm.
 * User: Ricky
 * Date: 2017/5/15
 * Time: 11:23
 */
namespace Helper;

class Page{

    /**
     * 当前页码
     */
    public $page = 1;

    /**
     * 每页显示条数
     */
    public $pagesize;

    /**
     * 总记录条数
     * @var int
     */
    public $total = 0;

    /**
     * 最大页码
     */
    public $pagemax;

    /**
     * 构造函数
     * @param int $total 总记录数
     * @param int $pagesize 每页显示条数
     */
    function __construct($total, $pagesize = 10){
        if($this->pagemax = ceil(($this->total = $total) / $this->pagesize = $pagesize)){
            if(isset($_GET['p']) && ($this->page = abs($_GET['p'])) && ($this->pagemax < $this->page)){
                $this->page = $this->pagemax;
            }
        }
        $this->page || $this->page = 1;
    }

    /**
     * 分页
     */
    function limit(){
        return (($this->page - 1) * $this->pagesize).','.$this->pagesize;
    }

    /**
     * 处理连接
     */
    private $_link = '';
    private function _default_link($tpl=''){
        if($tpl) return $this->_link = $tpl;
        if(!$this->_link){
            $tGet = $_GET;
            if(isset($tGet['p'])) unset($tGet['p']);
            $tUrl = isset($_SERVER['REDIRECT_URL'])? $_SERVER['REDIRECT_URL']: '';
            $tHref = $tUrl.(empty($tGet)? '?p=': '?'.http_build_query($tGet).'&p=');
            $this->_link = ' <a href="'.urldecode($tHref).'%d">%s</a> ';
        }
        return $this->_link;
    }

    /**
     * 生成链接
     * @param $p 页码
     * @param $t 链接锚文本
     * @return string
     */
    private function _make_link($p, $t){
        $p = $this->pagemax? ($p > $this->pagemax? $this->pagemax: $p): 1;
        $link = strtr($this->_link, array('%d'=>$p, '%s'=>$t));
        if($this->page == $t){
            return '<li class="active disabled">'.$link.'</li>';}
        else if($this->page == $p){
            return '<li class="active disabled not-allow">'.$link.'</li>';
        }
        return '<li>'.$link.'</li>';
    }

    /**
     * 前台样式
     */
    private function showStyle($tPage){
        # 渲染分页
        $tHtml = '<div class="page">';
        # 上一页
        $tHtml .= $this->page > 1? strtr($this->_link, array('%d' => $this->page - 1, '%s' => '上一页')): '<span>上一页</span>';
        # 页码
        foreach($tPage as $v1){
            $tHtml .= $this->page == $v1? " <span class='active'>$v1</span> ": sprintf($this->_link, $v1, $v1);
        }
        # 下一页
        $tHtml .= $this->page < $this->pagemax? strtr($this->_link, array('%d' => $this->page + 1, '%s' => '下一页')): '<span>下一页</span>';
        return $tHtml . '</div>';
    }

    /**
     * 显示分页
     * @param string $tpl 链接模板
     * @param bool $is_admin
     *
     * @return string
     */
    function show($tpl = '', $is_admin = true){
        $this->_default_link($tpl);
        if($this->pagemax <= 1) return '';
        $tPage = array();
        # 当前之前
        $tMax = $this->pagemax - $this->page > 5? 5: 10 - $this->pagemax + $this->page;
        for ($i = 0; $i < $tMax; $i++) {
            if(($tNum = $this->page - $i) < 1) break;
            $tPage[] = $tNum;
        }
        $tPage && sort($tPage);
        # 当前之后
        ($tMax = 10 - ($tCnt = count($tPage))) < 5 && $tMax = 5;
        ($tMax > ($this->pagemax - $this->page)) && $tMax = $this->pagemax - $this->page;
        for ($i = 0; $i < $tMax; $tPage[] = ++$i + $this->page);
        if(!$is_admin){return $this->showStyle($tPage);}
        # 渲染分页
        $tHtml = '<ul class="pagination">';
        # 上一页
        $tHtml .= $this->_make_link($this->page-1? $this->page-1: 1, ' 上一页 ');
        # 页码
        foreach ($tPage as $v1) $tHtml .= $this->_make_link($v1, $v1);
        # 下一页
        $tHtml .= $this->_make_link($this->page+1, ' 下一页 ');
        return $tHtml.'</ul>';
    }

}