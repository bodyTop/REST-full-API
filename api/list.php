<?php

error_reporting(E_ERR | E_STRICT); 
ini_set("display_errors", "On"); 

//数据操作类
require('./common/dbhelper.php');
require('./common/base.php');
//数据操作类
require('listRequest.php');
//输出类
require('Response.php');
//获取数据
$data = Request::getRequest();
// //输出结果
Response::sendResponse($data);

