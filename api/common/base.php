<?php 

/**描述：Action 基类
 * 作者：mingdee团队
 * 官网：www.mingdee.cn
 */

class base
{
    public $p = 'p';
    public $pagesize=10;
    //序列化数据
    public function stringify($data=null)
    {
    	if(!$data) return false;
    	exit(json_encode($data,true));
    }

    //反序列化数据
    public function parse($data=null)
    {
    	if(!$data)return false;
    	return json_decode($data, true);
    }

    /**
     * 分页处理
     * @param unknown $count 数据条数
     * @param unknown $pagesize 多少条分一页
     */
    public function page($count=null,$pagesize=5){
        $maxItem = ceil($count/$pagesize);
        $p = $_REQUEST[$this->p];
        $p = isset($p) ? $p-1 : 1-1;
        $begin = $p*$pagesize;
        $info = array();
        $info['begin'] = $begin;
        $info['end'] = $this->pagesize;
        $info['page'] = $p+1;
        $info['pagesize'] = $this->pagesize;
        $info['totalpage'] = $maxItem;
        return $info;
    }

    /**
     * 检验字段
     * @param unknown $data 当前数据
     * @param unknown $fields 需要检验的字段
     */
    public function checkFields($data = array(), $fields = array())
    {
        if (empty($data)){
            $data = array('status'=>'201','msg'=>'缺少参数');
            $this->stringify($data);
        }
        foreach ($data as $k => $val)
        {
            if (!in_array($k, $fields))
            {
                unset($data[$k]);
            }
        }
        return $data;
    }

    public function mdate($time = NULL) {
        $text = '';
        $time = $time === NULL || $time > time() ? time() : intval($time);
        $t = time() - $time; //时间差 （秒）
        $y = date('Y', $time)-date('Y', time());//是否跨年
        switch($t){
            case $t == 0:
                $text = '刚刚';
                break;
            case $t < 60:
                $text = $t . '秒前'; // 一分钟内
                break;
            case $t < 60 * 60:
                $text = floor($t / 60) . '分钟前'; //一小时内
                break;
            case $t < 60 * 60 * 24:
                $text = floor($t / (60 * 60)) . '小时前'; // 一天内
                break;
            case $t < 60 * 60 * 24 * 3:
                $text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
                break;
            case $t < 60 * 60 * 24 * 30:
                $text = date('m月d日 H:i', $time); //一个月内
                break;
            case $t < 60 * 60 * 24 * 365&&$y==0:
                $text = date('m月d日', $time); //一年内
                break;
            default:
                $text = date('Y年m月d日', $time); //一年以前
                break;
        }
    
        return $text;
    }
}