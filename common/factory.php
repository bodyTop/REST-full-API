<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2018/11/25
 * Time: 下午3:08
 */

namespace common;


use database\DBHelper;
use database\DBRedis;

class factory
{


    static function createDatabase($type)
    {
        if ($type == 'redis')
        {
            $db = Register::get($type);
            if (!$db)
            {
                $db = DBRedis::getInstance();
                Register::set($type,$db);
            }
            return $db;
        }

        if ($type == 'mysqli')
        {
            $db = Register::get($type);
            if (!$db)
            {
                $db = DBHelper::getInstance();
                Register::set($type,$db);
            }
            return $db;
        }
    }
}