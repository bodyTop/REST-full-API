<?php

class Request
{
    //允许的请求方式
    private static $method_type = array('get', 'post', 'put', 'patch', 'delete');
    //测试数据
    private static $test_class = array(
    );

    public static function getRequest()
    {
        //请求方式
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (in_array($method, self::$method_type)) {
            //调用请求方式对应的方法
            $data_name = $method . 'Data';
            return self::$data_name($_REQUEST);
        }
        return false;
    }

    //GET 获取信息
    private static function getData($request_data)
    {
        if (!empty($request_data['token'])) {
            $token = $request_data['token'];
            $model = new DBHelper();
            $base = new base();
            $sql = "SELECT * FROM `user` WHERE token='$token'";
            $result = $model->query($sql);
            if (!$result){
                $data['code'] = 203;
                $data['message'] = "SIGN ERROR";
                return self::$test_class;//返回新生成的资源对象
            }else{
            $sql = "SELECT name,head,content,time FROM `user_content` as A JOIN `user` as B ON A.user_id=B.id WHERE is_del='0' and is_public='1' ";
            $count = count($model->query($sql));
            $page_data = $base->page($count,$base->pagesize);
            $sql = "SELECT name,head,content,time FROM `user_content` as A JOIN `user` as B ON A.user_id=B.id WHERE is_del='0' and is_public='1' order by time desc limit ".$page_data['begin'].','.$page_data['end'];
            $listdata = $model->query($sql);
            foreach ($listdata as $k=>$v){
                $listdata[$k]['time'] = $base->mdate(strtotime($v['time']));
            }
            if ($page_data['totalpage']<$page_data['page']) unset($listdata);
            $info['list_data'] = $listdata?$listdata:array();
            $info['page'] = $page_data['page'];
            $info['pagesize'] = $base->pagesize;
            $info['totalpage'] = $page_data['totalpage'];
            if ($info)
            {
                $data['code'] = 200;
                $data['message'] = "OK";
                $data['data'] = $info;
                self::$test_class = $data;
                return self::$test_class;//返回新生成的资源对象
            }
            else
            {
                $data['code'] = 303;
                $data['message'] = "MYSQL ERROR";
                self::$test_class = $data;
                return self::$test_class;//返回新生成的资源对象
            }
            }
        } else {
            $data['code'] = 204;
            $data['message'] = "NOT PARAM";
            self::$test_class = $data;
            return self::$test_class;//返回新生成的资源对象
        }
    }




}