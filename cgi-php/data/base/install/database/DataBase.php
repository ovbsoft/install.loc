<?php

namespace Run\data\base\install\database;

use Run\panel\core\corp\Path;

class DataBase extends Tables {

    private $le, $lw, $post, $wg, $list = ['host', 'user', 'pass', 'base'];

    public function __construct()
    {
        $this->lang('database');
        $tmp = file_exists(Path::SZ . 'tmp.sz');
        $tmp ? parent::tables() : parent::view($this->_content());
    }

    protected function _content()
    {
        $this->le = require 'lang/' . $this->lang . '.php';
        $view = $this->_view($this->_post());
        return str_replace(
                array_keys($view),
                array_values($view),
                file_get_contents(__DIR__ . '/database.tpl')
        );
    }

    private function _post()
    {
        foreach ($this->list as $v) {
            $this->post[$v] = '';
        }
        if (filter_has_var(0, 'post')) {
            $wg = true;
            foreach ($this->list as $v) {
                $this->post[$v] = trim(filter_input(0, $v));
                !empty($this->post[$v]) ?: $wg = false;
            }
            if ($this->_empty()) {
                $this->lw = require 'lang/wg/' . $this->lang . '.php';
                $wg ? $this->_mysql() : $this->_warning($this->lw['form']);
                boolval($this->wg) ?: $this->_save();
            }
        }
    }

    private function _empty()
    {
        $empty = false;
        foreach ($this->list as $v) {
            empty($this->post[$v]) ?: $empty = true;
        }
        return $empty;
    }

    private function _mysql()
    {
        $mysql = new \mysqli(
                $this->post['host'],
                $this->post['user'],
                $this->post['pass'],
                $this->post['base']
        );
        $mysql->close() ?: $this->_warning($this->lw['mysql']);
    }

    private function _warning($w)
    {
        $this->wg = str_replace('[W]', $w, require Path::HTML . 'wg.php');
    }

    private function _save()
    {
        $sz = Path::SZ . 'tmp.sz';
        $data = serialize($this->post);
        if (boolval(file_put_contents($sz, $data)) === false) {
            exit(
                    'Не удалось ввести данные в файл : ' .
                    '~/data/sz/tmp.sz'
            );
        }
        $this->_header();
    }

    private function _header()
    {
        header('Location: /');
        exit;
    }

    private function _view()
    {
        return [
            '{ LE:HOST }' => $this->le['host'],
            '{ HOST:PH }' => $this->le['host_ph'],
            '{ HOST }' => $this->post['host'],
            '{ LE:USER }' => $this->le['user'],
            '{ USER:PH }' => $this->le['user_ph'],
            '{ USER }' => $this->post['user'],
            '{ LE:PASS }' => $this->le['pass'],
            '{ PASS:PH }' => $this->le['pass_ph'],
            '{ PASS }' => $this->post['pass'],
            '{ LE:BASE }' => $this->le['base'],
            '{ BASE:PH }' => $this->le['base_ph'],
            '{ BASE }' => $this->post['base'],
            '{ WARNING }' => $this->wg ?? '',
            '{ LE:SAVE-UPP }' => $this->le['save-upp']
        ];
    }

}
