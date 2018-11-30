<?php
class Loader
{

    static function autoload($class)
    {
        require __DIR__.'/'.str_replace('\\', '/', $class).'.php';
    }
}