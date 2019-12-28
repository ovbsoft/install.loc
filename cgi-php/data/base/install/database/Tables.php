<?php

namespace Run\data\base\install\database;

use Run\Root,
    Run\data\base\Mysql;

class Tables extends \Run\data\base\install\view\View {

    private $le, $mysql,
            $access = '
CREATE TABLE `db_access` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`access` VARCHAR(32) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`access`)
) ENGINE = InnoDB;',
            $user = '
CREATE TABLE `db_user` (
	`id` INT(11) NOT NULL AUTO_INCREMENT, 
	`id_access` INT(11) NOT NULL, 
	`user` VARCHAR(255) NOT NULL, 
	`created_date` INT(11) NOT NULL, 
	PRIMARY KEY (`id`), 
	INDEX (`id_access`), 
	UNIQUE (`user`)
) ENGINE = InnoDB;',
            $user_connect = '
ALTER TABLE
        `db_user`
ADD
        FOREIGN KEY (`id_access`)
        REFERENCES `db_access`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
            $mail = '
CREATE TABLE `db_mail` (
	`id_user` INT(11) NOT NULL, 
	`mail` VARCHAR(255) NOT NULL, 
	`pass` VARCHAR(32) NOT NULL, 
	`timestamp` VARCHAR(11) NOT NULL, 
	INDEX (`id_user`), 
	UNIQUE (`mail`)
) ENGINE = InnoDB;',
            $mail_connect = '
ALTER TABLE
	`db_mail`
ADD
	FOREIGN KEY (`id_user`)
        REFERENCES `db_user`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;';

    protected function tables()
    {
        $sz = Root::SZ . 'data.base.sz';
        $tmp = Root::SZ . 'tmp.sz';
        $data_base_sz = file_get_contents($tmp);
        unlink($tmp);
        if (boolval(file_put_contents($sz, $data_base_sz)) === false) {
            exit(
                    'Не удалось ввести данные в файл : ' .
                    '~/data/sz/data.base.sz'
            );
        }
        $this->_mysql();
    }

    private function _mysql()
    {
        $this->mysql = Mysql::mysql();
        $this->_access();
    }

    private function _access()
    {
        if ($this->mysql->query($this->access) === true) {
            $this->_user();
        } else {
            $this->mysql->close();
            exit('Не создана таблица [access]');
        }
    }

    private function _user()
    {
        if ($this->mysql->query($this->user) === true) {
            if ($this->mysql->query($this->user_connect) === true) {
                $this->_mail();
            } else {
                $this->mysql->close();
                exit('Не создана связь таблиц [user > access]');
            }
        } else {
            $this->mysql->close();
            exit('Не создана таблица [user]');
        }
    }

    private function _mail()
    {
        if ($this->mysql->query($this->mail) === true) {
            if ($this->mysql->query($this->mail_connect) === true) {
                $this->mysql->close();
                $this->_header();
            } else {
                $this->mysql->close();
                exit('Не создана связь таблиц [mail > user]');
            }
        } else {
            $this->mysql->close();
            exit('Не создана таблица [mail]');
        }
    }

    private function _header()
    {
        header('Location: /');
        exit;
    }

}
