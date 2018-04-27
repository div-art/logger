<?php

namespace DivArt\Logger;

use App\Http\Controllers\Controller;
use DivArt\Logger\Facades\Logger;

/**
 * Class LogController
 * @package DivArt\Logger
 */
class LogController extends Controller
{
    /**
     * log files
     * @var array
     */
    public $files = [];

    /**
     * all dates
     * @var array
     */
    public $dates = [];

    /**
     * all types
     * @var array
     */
    public $types = [];

    /**
     * all marks
     * @var array
     */
    public $marks = [];

    /**
     * LogController constructor.
     */
    public function __construct()
    {
        Logger::deleteExpiredLogs();
    }

    /**
     * show all available logs
     * @param string $type
     * @param null $date
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLogs($type = 'all', $date = null)
    {
        $client = Logger::createLocalStorageDriver();

        $rootPath = Logger::getRootPath();

        $allFiles = $client->files();

        $filterParams = Logger::getFilterParams($allFiles, $rootPath, $this->dates, $this->types, $this->marks);

        $this->dates = $filterParams['dates'];

        $this->types = $filterParams['types'];

        $this->marks = $filterParams['marks'];

        $this->files = Logger::fillLogFiles($rootPath, $client, $date, $allFiles);

        return view('div-art::logs', [
            'files' => $this->files,
            'dates' => $this->dates,
            'types' => $this->types,
            'marks' => $this->marks
        ]);
    }

    /**
     * filter logs by params
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function filterLogs()
    {
        $date = request('date');

        $type = request('type');

        $mark = request('mark');

        if (is_null($date) && is_null($type) && is_null($mark)) {
            return redirect()->route('all-logs', ['type'=>'all']);
        }

        $rootPath = Logger::getRootPath();

        $client = Logger::createLocalStorageDriver();

        $allFiles = $client->files();

        $filterParams = Logger::getFilterParams($allFiles, $rootPath, $this->dates, $this->types, $this->marks);

        $this->dates = $filterParams['dates'];

        $this->types = $filterParams['types'];

        $this->marks = $filterParams['marks'];

        $this->files = Logger::fillLogFiles($rootPath, $client, $date, $allFiles);

        $this->files = Logger::filterLogs($this->files, $type, $mark);

        return view('div-art::logs', [
            'files' => $this->files,
            'dates' => $this->dates,
            'types' => $this->types,
            'marks' => $this->marks
        ]);
    }

    /**
     * delete log in file
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLog()
    {
        $date = request('date');

        $type = request('type');

        $time = request('time');

        $line = request('line');

        $file_ = request('file');

        $client = Logger::createLocalStorageDriver();

        $rootPath = Logger::getRootPath();

        $file = file("$rootPath/$date.json");

        $client->delete("$date.json");

        foreach ($file as $k => $v) {
            $file[$k] = json_decode($file[$k]);

            if ($file[$k] && ! ($file[$k]->type == $type && $file[$k]->time == $time &&
                    $file[$k]->line == $line && $file[$k]->file == $file_)) {
                Logger::store($file[$k], $date);
            }
        }

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * delete log file
     * @param $date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLogFile($date)
    {
        $client = Logger::createLocalStorageDriver();

        if ($client->exists("$date.json")) {
            $client->delete("$date.json");
        }

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * delete all file logs
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAllLogs()
    {
        $client = Logger::createLocalStorageDriver();

        $files = $client->files();

        $client->delete($files);

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * delete log file by params
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLogFileByParam()
    {
        $date = request('date');

        $type = request('type');

        $client = Logger::createLocalStorageDriver();

        $rootPath = Logger::getRootPath();

        $files = $client->files();

        if ( ! is_null($date) && ! is_null($type)) {
            $file = file("$rootPath/$date.json");

            $client->delete("$date.json");

            foreach ($file as $k => $v) {
                $file[$k] = json_decode($file[$k]);

                if ($file[$k] && $file[$k]->type != $type) {
                    Logger::store($file[$k], $date);
                }
            }
        }

        if (is_null($type) && ! is_null($date)) {
            $client->delete("$date.json");
        }

        if (is_null($date) && ! is_null($type)) {
            foreach ($files as $file) {
                $data = file("$rootPath/$file");

                $client->delete("$file");

                foreach ($data as $k => $v) {
                    $data[$k] = json_decode($data[$k]);

                    if ($data[$k] && $data[$k]->type != $type) {
                        Logger::store($data[$k], str_replace('.json', '', $file));
                    }
                }
            }
        }

        return redirect()->route('all-logs', ['type'=> 'all']);
    }
}