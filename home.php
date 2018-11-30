<?php
error_reporting(E_ERROR | E_STRICT);
ini_set("display_errors", "On");

require('Loader.php');
spl_autoload_register("Loader::autoload");
//数据操作类
require('homeRequest.php');
//输出类
require('Response.php');
//获取数据
$data = Request::getRequest();
// //输出结果
Response::sendResponse($data);