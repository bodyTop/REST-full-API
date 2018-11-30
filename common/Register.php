<?php
namespace common;

class Register
{
    protected static $objects;

    static function set($alias, $object)
    {
        self::$objects[$alias] = $object;
    }

    static function get($key)
    {
        if (!isset(self::$objects[$key]))
        {
            return false;
        }
        return self::$objects[$key];
    }

    static function _unset($alias)
    {
        unset(self::$objects[$alias]);
    }

    static function all()
    {
        return self::$objects;
    }
}
