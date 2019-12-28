<?php

namespace Run;

class Branch {

    use tech\traits\Filter;

    private $ext, $param, $pattern = '/^[\w\-\.\/\?\&\=\:]+$/iu';

    public function __construct()
    {
        new data\sz\Data;
        $run = unserialize(file_get_contents(Root::SZ . 'branch.run.sz'));
        $request = $this->server_request_uri();
        if ($run['admin']) {
            $this->ext = $run['ext'];
            $this->_branch($this->_request($request));
        } else {
            new data\base\install\Run($request);
        }
    }

    private function _request($request)
    {
        $this->param['request'] = $request;
        $query = strrchr($this->param['request'], '?');
        $urn = $query ? (
                substr($this->param['request'], 0, - strlen($query))
                ) : $this->param['request'];
        $ext = strrchr($urn, '.');
        $path = $ext ? substr($urn, 1, - strlen($ext)) : substr($urn, 1);
        $this->param['path'] = empty($path) ? false : $path;
        $this->param['ext'] = $ext;
        $this->param['error'] = $this->_error();
    }

    private function _error()
    {
        return (
                preg_match($this->pattern, $this->param['request']) === 0 or
                preg_match('/\/\//', $this->param['request']) === 1 or
                preg_match('/[\/]$/', $this->param['path']) === 1 or
                $this->param['ext'] and empty($this->param['path'])
                ) ? true : false;
    }

    private function _branch()
    {
        var_dump($this->ext, $this->param);
    }

}
