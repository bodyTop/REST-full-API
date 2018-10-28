<?php
/*
 * 发送：
 * GET  http://localhost/restful/class  列出所有班级
 * GET  http://localhost/restful/class/1    获取指定班的信息
 * POST http://localhost/restful/class?name=SAT班&count=23 新建一个班
 * PUT  http://localhost/restful/class/1?name=SAT班&count=23  更新指定班的信息（全部信息）
 * PATCH  http://localhost/restful/class/1?name=SAT班    更新指定班的信息（部分信息）
 * DELETE  http://localhost/restful/class/1 删除指定班
*/
error_reporting(E_ERR | E_STRICT); 
ini_set("display_errors", "On"); 
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
function p($array)
{
    dump($array, 1, '<pre style=font-size:14px;color:#00ae19;>', 0);
}

//数据操作类
require('./common/dbhelper.php');
//数据操作类
require('indexRequest.php');
//输出类
require('Response.php');

//获取数据
$data = Request::getRequest();
// //输出结果
Response::sendResponse($data);

