<?php

namespace Run\data\base\install;

use Run\Root;

class Run {

    public function __construct($request)
    {
        error_reporting(0);
        if ($request === '/') {
            if (file_exists(Root::SZ . 'data.base.sz')) {
                new admin\Admin;
            } else {
                new database\DataBase;
            }
        } else {
            header('Location: /');
            exit;
        }
    }

}
