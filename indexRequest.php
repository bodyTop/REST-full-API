<?php
/**
 * 数据操作类
 */
class Request
{
    //允许的请求方式
    private static $method_type = array('get', 'post', 'put', 'patch', 'delete');
    //测试数据
    private static $test_class = array(
//         1 => array('name' => '托福班', 'count' => 18),
//         2 => array('name' => '雅思班', 'count' => 20),
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
        if (!empty($request_data['uuid']))
        {
            $uuid = $request_data['uuid'];
            $model = \common\factory::createDatabase('mysqli'); //DBHelper();
            $redis = \common\factory::createDatabase('redis'); //DBHelper();
            $token = md5($uuid);

            $user_info = $redis->hGet('user',$token);
            if (!$user_info)
            {
                $sql = "SELECT * FROM `user` WHERE token='$token'";
                $result = $model->query($sql);
                if ($result){
                    $data['code'] = 200;
                    $data['message'] = "OK";
                    $data['data'] = array('token'=>$result[0]['token'],'name'=>$result[0]['name'],'head'=>$result[0]['head'],'id'=>$result[0]['id']);
                    self::$test_class = $data;
                    return self::$test_class;//返回新生成的资源对象
                }else{
                    $register_time = date('Y-m-d H:i:s',time());
                    $name = '未命名';
                    $sql = "INSERT INTO `user` (`name`,`register_time`,`token`,`uuid`) values ('$name','$register_time','$token','$uuid')";
                    $result = explode('.',$model->execute($sql));
                    if ($result[0]>0)
                    {
                        $user_data = array($name,$register_time,$token,$uuid);
                        $redis->hSet('user',$token,json_encode($user_data));
                        $data['code'] = 200;
                        $data['message'] = "OK";
                        $data['data'] = array('token'=>$token,'name'=>$name,'head'=>'head.png','id'=>$result[1]);
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
            }
        } else {
            $data['code'] = 204;
            $data['message'] = "NOT PARAM";
            self::$test_class = $data;  
            return self::$test_class;//返回新生成的资源对象
        }
    }

    //POST /class：新建一个班
    private static function postData($request_data)
    {
        $token = $request_data['token'];
        $content = $request_data['content'];
        $time = date('Y-m-d H:i:s',time());
        $id = $request_data['id'];
        if (!$id or !$token or !trim($content) or !$time){
            $data['code'] = 204;
            $data['message'] = "NOT PARAM";
            self::$test_class = $data;
            return self::$test_class;//返回新生成的资源对象
        }
        
        $model = \common\factory::createDatabase('mysqli');//DBHelper();
        $redis = \common\factory::createDatabase('redis');//DBHelper();

        $user_info = $redis->hGet('user',$token);
        if (!$user_info){
            $sql = "SELECT * FROM `user` WHERE token='$token'";
            $result = $model->query($sql);
            if (!$result){
                $data['code'] = 203;
                $data['message'] = "SIGN ERROR";
                self::$test_class = $data;
                return self::$test_class;//返回新生成的资源对象
            }
        }
        $is_public = 1;
        $sql = "INSERT INTO `user_content` (`user_id`,`content`,`time`,`is_public`) values ('$id','$content','$time','$is_public')";
        $result = explode('.',$model->execute($sql));
        if ($result[0]>0)
        {
            $content_data = array($id,$content,$time,$is_public);
            $redis->lPush('listdata',json_encode($content_data));
            $redis->lPush('homedata',json_encode($content_data));
            $data['code'] = 200;
            $data['message'] = "OK";
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

    //PUT /class/ID：更新某个指定班的信息（全部信息）
    private static function putData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id == 0) {
            return false;
        }
        $data = array();
        if (!empty($request_data['name']) && isset($request_data['count'])) {
            $data['name'] = $request_data['name'];
            $data['count'] = (int)$request_data['count'];
            self::$test_class[$class_id] = $data;
            return self::$test_class;
        } else {
            return false;
        }
    }

    //PATCH /class/ID：更新某个指定班的信息（部分信息）
    private static function patchData($request_data)
    {        
        $token = $request_data['token'];
        $name = $request_data['name'];
        if (!$token) {
            $data['code'] = 204;
            $data['message'] = "NOT PARAM";
            self::$test_class = $data;  
            return self::$test_class;//返回新生成的资源对象
        }elseif (!trim($name)){
            $data['code'] = 204;
            $data['message'] = "NOT PARAM";
            self::$test_class = $data;
            return self::$test_class;//返回新生成的资源对象
        }
        $model = \common\factory::createDatabase('mysqli');
        $redis = \common\factory::createDatabase('redis');
        $sql ="UPDATE `user` SET name = '$name' WHERE token = '$token'";
        $result = explode('.',$model->execute($sql));
        if ($result[0]>=0)
        {
            $redis->del('homedata');
            $redis->del('listdata');
            $data['code'] = 200;
            $data['message'] = "OK";
            self::$test_class = $data;
            return self::$test_class;//返回新生成的资源对象
        }
        else
        {
            $data['code'] = 303;
            $data['message'] = "MYSQL ERROR";
            return self::$test_class;//返回新生成的资源对象
        }
    }

    //DELETE /class/ID：删除某个班
    private static function deleteData($request_data)
    {
        $class_id = (int)$request_data['class'];
        if ($class_id == 0) {
            return false;
        }
        unset(self::$test_class[$class_id]);
        return self::$test_class;
    }
}