<?php

namespace Run\data\base\install\admin;

use Run\data\base\Mysql,
    Run\panel\core\corp\Path;

class Save extends \DateTime {

    use \Run\tech\traits\Filter,
        \Run\tech\traits\Hash;

    private $mail, $user, $pass, $timestamp, $mysql,
            $query_access = "
INSERT INTO `db_access` (`id`, `access`) 
VALUES 
	(NULL, 'root')",
            $query_user = "
INSERT INTO `db_user` (`id`, `id_access`, `user`, `created_date`) 
VALUES 
	(NULL, '1', '[U]', '[T]')",
            $query_mail = "
INSERT INTO `db_mail` (
	`id_user`, `mail`, `pass`, `timestamp`
) 
VALUES 
	('1', '[M]', '[P]', '[T]')";

    public function __construct($param)
    {
        parent::__construct();
        $this->mail = $param['mail'];
        $this->user = $param['user'];
        $this->pass = $param['pass'];
        $this->timestamp = $this->getTimestamp();
        $this->mysql = Mysql::mysql();
        $this->_access();
    }

    private function _access()
    {
        if ($this->mysql->query($this->query_access) === true) {
            $this->_user();
        } else {
            $this->mysql->close();
            exit('Не создан доступ [root]');
        }
    }

    private function _user()
    {
        $s = ['[U]', '[T]'];
        $r = [$this->user, $this->timestamp];
        $query = str_replace($s, $r, $this->query_user);
        if ($this->mysql->query($query) === true) {
            $this->_mail();
        } else {
            $this->mysql->close();
            exit('Не создан администратор [' . $this->user . ']');
        }
    }

    private function _mail()
    {
        $s = ['[M]', '[P]', '[T]'];
        $pass = password_hash($this->pass, PASSWORD_DEFAULT);
        $r = [$this->mail, $pass, $this->timestamp];
        $query = str_replace($s, $r, $this->query_mail);
        if ($this->mysql->query($query) === true) {
            $this->mysql->close();
            $this->_hash();
        } else {
            $this->mysql->close();
            exit('Не создана эл. почта [' . $this->mail . ']');
        }
    }

    private function _hash()
    {
        $hash = $this->hash(32);
        $sz = [
            'hash' => $hash,
            'time' => $this->timestamp,
            'agent' => $this->server_user_agent()
        ];
        $hash_sz = Path::HASH . $this->user . '.sz';
        file_put_contents($hash_sz, serialize($sz));
        $this->_setcookie($hash);
    }

    private function _setcookie($hash)
    {
        $exp = explode('.', $this->server_http_host());
        $exp[0] !== 'www' ?: array_shift($exp);
        $domain = '.' . implode('.', $exp);
        setcookie('panel:user', $this->user, 0, '/', $domain, true);
        setcookie('panel:hash', $hash, 0, '/', $domain, true);
        $this->_branch();
    }

    private function _branch()
    {
        $branch_sz = Path::SZ . 'branch.run.sz';
        $branch = unserialize(file_get_contents($branch_sz));
        $branch['admin'] = $this->user;
        file_put_contents($branch_sz, serialize($branch));
        $this->_header($branch['ext']);
    }

    private function _header($ext)
    {
        header('Location: /personal' . $ext);
        exit;
    }

}
