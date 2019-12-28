<?php

namespace Run\data\base;

use Run\Root;

class Mysql {

    public static function mysql()
    {
        $db = unserialize(file_get_contents(Root::SZ . 'data.base.sz'));
        return new \mysqli(
                $db['host'],
                $db['user'],
                $db['pass'],
                $db['base']
        );
    }

}
