<?php

namespace DivArt\Logger;

use DateTime;
use Exception;
use Illuminate\Support\Facades\Storage;

/**
 * Class Helper
 * @package DivArt\Logger
 */
class Helper
{
    /**
     * default folder name of logs
     * @var string $folder
     */
    public $folder = 'logger';

    /**
     * lifetime of logs in days
     * @var int $expireDays
     */
    public $expireDays = 7;

    /**
     * root path for storage driver
     * @var string $rootPath
     */
    public $rootPath;

    /**
     * Helper constructor.
     */
    public function __construct()
    {
        $this->rootPath = storage_path($this->folder);

        if (config('logger.path')) {
            $this->folder = config('logger.path');

            $this->rootPath = storage_path($this->folder);
        }

        if (config('logger.expire_days')) {
            $this->expireDays = config('logger.expire_days');
        }

        $this->addToGitIgnore();

        $this->deleteExpiredLogs();
    }

    /**
     * method handles storing the record into the log file
     * @param array $record
     * @param string $filename
     * @throws Exception
     */
    public function store($record, $filename)
    {
        try {
            $client = $this->createLocalStorageDriver();

            if ( ! $client->exists("$filename.json")) {
                $client->put("$filename.json", json_encode($record));
            } else {
                $client->append("$filename.json", json_encode($record));
            }
        }
        catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * method returns formatted $data for logging
     * @param array $debugBacktrace
     * @param string $type
     * @param string $mark
     * @param array $data
     * @return array
     */
    public function formattingRecord($debugBacktrace, $type = '', $mark = '', $data = [])
    {
        $file = $debugBacktrace[1]['file'];

        $line = $debugBacktrace[1]['line'];

        $record = [
            'type' => $type,
            'mark' => $mark,
            'time' => date('H:i:s'),
            'file' => $file,
            'line' => $line,
            'data' => $data
        ];

        return $record;
    }

    /**
     * method checks if logs expired if true then delete them
     */
    public function deleteExpiredLogs()
    {
        $client = $this->createLocalStorageDriver();

        $allFiles = $client->files();

        foreach ($allFiles as $file) {
            $fileDate = new DateTime(str_replace('.json', '', $file));

            $currentDate = new DateTime(date('d-m-Y'));

            $interval = $fileDate->diff($currentDate);

            if ($interval->days > $this->expireDays) {
                $client->delete($file);
            }
        }
    }

    /**
     * adding log folder to .gitignore
     */
    public function addToGitIgnore()
    {
        $client = $this->createLocalStorageDriver(base_path());

        $gitString = "/storage/$this->folder";

        if ($client->exists('.gitignore')) {
            $gIgnore = file(base_path('.gitignore'));

            if ( ! in_array($gitString, array_map('trim', $gIgnore))) {
                $client->append('.gitignore', $gitString);
            }
        } else {
            $client->put('.gitignore', $gitString);
        }
    }

    /**
     * create and return Storage local driver instance
     * @param null|string $rootPath
     * @return mixed
     */
    public function createLocalStorageDriver($rootPath = null)
    {
        if ( ! is_null($rootPath)) {
            $client = Storage::createLocalDriver(['root' => $rootPath]);
        } else {
            $client = Storage::createLocalDriver(['root' => $this->rootPath]);
        }

        return $client;
    }

    /**
     * return $this->rootPath
     * @return string
     */
    public function getRootPath()
    {
        return $this->rootPath;
    }

    /**
     * filling params for filter
     * @param $allFiles
     * @param $rootPath
     * @param $dates
     * @param $types
     * @param $marks
     * @return array
     */
    public function getFilterParams($allFiles, $rootPath, $dates, $types, $marks)
    {
        foreach ($allFiles as $file) {
            $dates[] = str_replace('.json', '', $file);

            foreach (file("$rootPath/$file") as $log) {
                $d = json_decode($log);

                $marks[] = $d->mark;

                $types[] = $d->type;
            }
        }

        $types = array_unique($types);

        $marks = array_unique($marks);

        return [
            'dates' => $dates,
            'types' => $types,
            'marks' => $marks
        ];
    }

    /**
     * fill log files
     * @param $rootPath
     * @param $client
     * @param $date
     * @param $allFiles
     * @return array
     */
    public function fillLogFiles($rootPath, $client, $date, $allFiles)
    {
        $logs = [];

        $files = [];

        if ( ! is_null($date)) {
            if ($client->exists("$date.json")) {
                foreach (file("$rootPath/$date.json") as $log) {
                    $files[$date][] = json_decode($log);
                }
            }

            return $files;
        } else {
            foreach ($allFiles as $file) {
                $files[$file] = file("$rootPath/$file");
            }

            foreach ($files as $file => $fileLogs) {
                foreach ($fileLogs as $log) {
                    $logs[str_replace('.json', '', $file)][] = json_decode($log);
                }
            }

            return $logs;
        }
    }

    /**
     * filter logs by type, mark
     * @param $files
     * @param $type
     * @param $mark
     * @return mixed
     */
    public function filterLogs($files, $type, $mark)
    {
        if ( ! is_null($type) && ! is_null($mark)) {
            foreach ($files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->type == $type && $log->mark == $mark) {
                        $tmp[$file][] = $log;
                    }
                }
            }

            if ( ! empty($tmp)) {
                $files = $tmp;
            }
        }


        if (is_null($mark) && ! is_null($type)) {
            foreach ($files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->type == $type) {
                        $tmp[$file][] = $log;
                    }
                }

            }

            if ( ! empty($tmp)) {
                $files = $tmp;
            }
        }

        if (is_null($type) && ! is_null($mark)) {
            foreach ($files as $file => $logs) {
                foreach ($logs as $log) {
                    if ($log->mark == $mark) {
                        $tmp[$file][] = $log;
                    }
                }
            }

            if ( ! empty($tmp)) {
                $files = $tmp;
            }
        }

        return $files;
    }
}