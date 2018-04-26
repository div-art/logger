<?php

namespace DivArt\Logger;

use Exception;

/**
 * Class Logger
 * @package DivArt\Logger
 */
class Logger extends Helper
{

    /**
     * logging simple record with user data
     * @param $data
     * @param string $mark
     * @throws Exception
     */
    public function save($data, $mark = '-')
    {
        if (is_null($data)) {
            throw new Exception('required parameter \'data\' cannot be null');
        }

        $filename = date('d-m-Y');

        $debugBacktrace = debug_backtrace();

        $record = $this->formattingRecord($debugBacktrace, 'simple', $mark, $data);

        $this->store($record, $filename);
    }

    /**
     * logging info record with user data
     * @param $data
     * @param string $mark
     * @throws Exception
     */
    public function info($data, $mark = '-')
    {
        if (is_null($data)) {
            throw new Exception('required parameter \'data\' cannot be null');
        }

        $filename = date('d-m-Y');

        $debugBacktrace = debug_backtrace();

        $record = $this->formattingRecord($debugBacktrace, 'info', $mark, $data);

        $this->store($record, $filename);
    }

    /**
     * logging critical record with user data
     * @param $data
     * @param string $mark
     * @throws Exception
     */
    public function danger($data, $mark = '-')
    {
        if (is_null($data)) {
            throw new Exception('required parameter \'data\' cannot be null');
        }

        $filename = date('d-m-Y');

        $debugBacktrace = debug_backtrace();

        $record = $this->formattingRecord($debugBacktrace, 'critical', $mark, $data);

        $this->store($record, $filename);
    }

    /**
     * logging success record with user data
     * @param $data
     * @param string $mark
     * @throws Exception
     */
    public function success($data, $mark = '-')
    {
        if (is_null($data)) {
            throw new Exception('required parameter \'data\' cannot be null');
        }

        $filename = date('d-m-Y');

        $debugBacktrace = debug_backtrace();

        $record = $this->formattingRecord($debugBacktrace, 'success', $mark, $data);

        $this->store($record, $filename);
    }


    //-------------------- additional log methods --------------------//

    /**
     * logging data from $request object
     * @param null|string $key
     * @throws Exception
     */
    public function request($key = null)
    {
        $filename = date('d-m-Y');

        $mark = "-";

        $debugBacktrace = debug_backtrace();

        $data = request()->toArray();

        if ( ! is_null($key) && array_key_exists($key, $data)) {
            $mark = "key: " . $key;
            $tmpArr[$key] = $data[$key];
            $data = $tmpArr;
        } else {
            $data = [];
            $data['key'] = 'not found';
        }

        $record = $this->formattingRecord($debugBacktrace, 'request', $mark, $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from request inputs
     * @param null|string $key
     * @throws Exception
     */
    public function input($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = request()->all();

        if (!is_null($key))
        {
            $data = request()->input($key);
        }

        $record = $this->formattingRecord($debug_backtrace, 'input', 'data from request inputs', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from request->json
     * @param null|string $key
     * @throws Exception
     */
    public function json($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = request()->json()->all();

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'json', 'data from request->json', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from $_POST array
     * @param null|string $key
     * @throws Exception
     */
    public function post($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = $_POST;

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'post', 'data from $_POST array', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from $_GET array
     * @param null|string $key
     * @throws Exception
     */
    public function get($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = $_GET;

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'get', 'data from $_GET array', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from php://input
     * @param null|string $key
     * @throws Exception
     */
    public function php($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $string = file_get_contents('php://input');

        $data = [];

        $pairs = explode('&', $string);

        foreach ($pairs as $pair)
        {
            $key_value_arr = explode('=', $pair);

            $data[$key_value_arr[0]] = $key_value_arr[1];
        }

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'php', 'php://input record', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from $_SERVER array
     * @param null|string $key
     * @throws Exception
     */
    public function server($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = $_SERVER;

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'server', 'data from $_SERVER array', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from $_COOKIE array
     * @param null|string $key
     * @throws Exception
     */
    public function cookies($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = $_COOKIE;

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'cookies', 'data from $_COOKIE array', $data);

        $this->store($record, $filename);
    }

    /**
     * logging data from $request->headers
     * @param null|string $key
     * @throws Exception
     */
    public function headers($key = null)
    {
        $filename = date('d-m-Y');

        $debug_backtrace = debug_backtrace();

        $data = request()->header();

        if (!is_null($key))
        {
            $tmp_arr = data_get($data, $key, $data);

            $data = $tmp_arr;
        }

        $record = $this->formattingRecord($debug_backtrace, 'headers', 'data from $request->header()', $data);

        $this->store($record, $filename);
    }

}