<?php

namespace DivArt\Logger;

use App\Http\Controllers\Controller;
use DivArt\Logger\Facades\Logger;
use Illuminate\Support\Facades\Storage;

/**
 * Class LogController
 * @package DivArt\Logger
 */
class LogController extends Controller
{
    public $logs = [];

    public $files = [];

    public $dates = [];

    public $types = [];

    public $marks = [];

    /**
     * LogController constructor.
     */
    public function __construct()
    {
        Logger::deleteExpiredLogs();
    }

    /**
     * @param string $type
     * @param null $date
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLogs($type = 'all', $date = null)
    {
        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

        $allFiles = $client->files();

        foreach ($allFiles as $file) {
            $this->dates[] = str_replace('.json', '', $file);

            foreach (file("$rootPath/$file") as $log) {
                $d = json_decode($log);

                $this->marks[] = $d->mark;

                $this->types[] = $d->type;
            }
        }

        $this->types = array_unique($this->types);

        $this->marks = array_unique($this->marks);

        if ( ! is_null($date)) {
            if ($client->exists("$date.json")) {
                foreach (file("$rootPath/$date.json") as $log) {
                    $this->files[$date][] = json_decode($log);
                }
            }
        } else {
            foreach ($allFiles as $file) {
                $this->files[$file] = file("$rootPath/$file");
            }

            foreach ($this->files as $file => $fileLogs) {
                foreach ($fileLogs as $log) {
                    $this->logs[str_replace('.json', '', $file)][] = json_decode($log);
                }
            }

            $this->files = $this->logs;
        }

        return view('div-art::logs', [
            'files' => $this->files,
            'dates' => $this->dates,
            'types' => $this->types,
            'marks' => $this->marks
        ]);
    }

    /**
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

        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

        $allFiles = $client->files();

        foreach ($allFiles as $file) {
            $this->dates[] = str_replace('.json', '', $file);

            foreach (file("$rootPath/$file") as $log) {
                $d = json_decode($log);
                $this->marks[] = $d->mark;
                $this->types[] = $d->type;
            }
        }

        $this->types = array_unique($this->types);

        $this->marks = array_unique($this->marks);

        if ( ! is_null($date)) {
            if ($client->exists("$date.json")) {
                foreach (file("$rootPath/$date.json") as $log) {
                    $this->files[$date][] = json_decode($log);
                }
            }
        } else {
            foreach ($allFiles as $file) {
                $this->files[$file] = file("$rootPath/$file");
            }

            foreach ($this->files as $file => $fileLogs) {
                foreach ($fileLogs as $log) {
                    $this->logs[str_replace('.json', '', $file)][] = json_decode($log);
                }
            }

            $this->files = $this->logs;
        }

        if ( ! is_null($type) && ! is_null($mark)) {
            foreach ($this->files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->type == $type && $log->mark == $mark) {
                        $tmp[$file][] = $log;
                    }
                }
            }

            if ( ! empty($tmp)) {
                $this->files = $tmp;
            }
        }


        if (is_null($mark) && ! is_null($type)) {
            foreach ($this->files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->type == $type) {
                        $tmp[$file][] = $log;
                    }
                }

            }

            if ( ! empty($tmp)) {
                $this->files = $tmp;
            }
        }

        if (is_null($type) && ! is_null($mark)) {
            foreach ($this->files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->mark == $mark) {
                        $tmp[$file][] = $log;
                    }
                }
            }

            if ( ! empty($tmp)) {
                $this->files = $tmp;
            }
        }

        return view('div-art::logs', [
            'files' => $this->files,
            'dates' => $this->dates,
            'types' => $this->types,
            'marks' => $this->marks
        ]);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLog()
    {
        $date = request('date');

        $type = request('type');

        $time = request('time');

        $line = request('line');

        $file_ = request('file');

        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

        $file = file("$rootPath/$date.json");

        $client->delete("$date.json");

        foreach ($file as $k => $v) {
            $file[$k] = json_decode($file[$k]);

            if ($file[$k] && ! ($file[$k]->type == $type && $file[$k]->time == $time && $file[$k]->line == $line && $file[$k]->file == $file_)) {
                Logger::store($file[$k], $date);
            }
        }

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * @param $date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLogFile($date)
    {
        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

        if ($client->exists("$date.json")) {
            $client->delete("$date.json");
        }

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAllLogs()
    {
        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

        $files = $client->files();

        $client->delete($files);

        return redirect()->route('all-logs', ['type'=> 'all']);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLogFileByParam()
    {
        $date = request('date');

        $type = request('type');

        $rootPath = storage_path('logger');

        if (config('logger.path')) {
            $rootPath = storage_path(config('logger.path'));
        }

        $client = Storage::createLocalDriver(['root' => $rootPath]);

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