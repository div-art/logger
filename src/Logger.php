<?php

namespace Divart\Logger;

use Divart\Logger\FileManager;
use Illuminate\Support\ServiceProvider;

class Logger extends FileManager
{
    public function saveLog($data, $mark, $type)
    {
        $this->createFolder();

        $data = array(
            'time' => date('Y-m-d-H-i-s'),
            'type' => $type,
            'mark' => $mark,
            'data' => $data
        );

        $this->saveDataInLogFile($data);
    }

    public function save($data, $mark = '')
    {
        $type = 'save';
        $this->saveLog($data, $mark, $type);
    }

    public function info($data, $mark = '')
    {
        $type = 'info';
        $this->saveLog($data, $mark, $type);
    }

    public function danger($data, $mark = '')
    {
        $type = 'danger';
        $this->saveLog($data, $mark, $type);
    }

    public function success($data, $mark = '')
    {
        $type = 'success';
        $this->saveLog($data, $mark, $type);
    }

    public function request($key = NULL)
    {
        $data = (is_null($key)) ? request() : request()->only($key);
        $mark = (is_null($key) or is_object($key)) ? 'request' : $key;
        $type = 'request';

        $this->saveLog($data, $mark, $type);
    }

    public function input($key = NULL)
    {
        $data = (is_null($key)) ? request()->all() : request()->input($key);
        $mark = (is_null($key)) ? 'input' : $key;
        $type = 'input';

        $this->saveLog($data, $mark, $type);
    }

    public function json($key = NULL)
    {
        $data = (is_null($key)) ? request()->json()->all() : request()->json()->only($key);
        $mark = (is_null($key)) ? 'json' : $key;
        $type = 'json';

        $this->saveLog($data, $mark, $type);
    }

    public function post($key = NULL)
    {
        $data = (is_null($key)) ? $_POST : $_POST[$key];
        $mark = (is_null($key)) ? 'post' : $key;
        $type = 'post';

        $this->saveLog($data, $mark, $type);
    }

    public function get($key = NULL)
    {
        $data = (is_null($key)) ? $_GET : $_GET[$key];
        $mark = (is_null($key)) ? 'get' : $key;
        $type = 'get';

        $this->saveLog($data, $mark, $type);
    }

    public function php($key = NULL)
    {
        $data_input = file_get_contents('php://input');
        $data_input = explode('&', $data_input);

        for ($i = 0; $i < count($data_input); $i++) {
            $temp_data = explode('=', $data_input[$i]);
            $data[$temp_data[0]] = $temp_data[1];
        }

        $mark = (is_null($key)) ? 'php://input' : $data = $data[$key];
        $type = 'php';

        $this->saveLog($data, $mark, $type);
    }

    public function server($key = NULL)
    {
        $data = (is_null($key)) ? $_SERVER : $_SERVER[$key];
        $mark = (is_null($key)) ? 'server' : $key;
        $type = 'server';

        $this->saveLog($data, $mark, $type);
    }

    public function cookies($key = NULL)
    {
        $data = (is_null($key)) ? $_COOKIE : $_COOKIE[$key];
        $mark = (is_null($key)) ? 'cookies' : $key;
        $type = 'cookies';

        $this->saveLog($data, $mark, $type);
    }

    public function headers($key = '')
    {
        $data = (is_null($key)) ? request()->header() : request()->header($key);
        $mark = (is_null($key)) ? 'headers' : $key;
        $type = 'headers';

        $this->saveLog($data, $mark, $type);
    }

}