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
            $client = Storage::createLocalDriver(['root' => $this->rootPath]);

            $this->addToGitIgnore($this->folder);

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
        if (config('logger.expire_days')) {
            $this->expireDays = config('logger.expire_days');
        }

        $client = Storage::createLocalDriver(['root' => $this->rootPath]);

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
     * @param string $folder
     */
    public function addToGitIgnore($folder)
    {
        $client = Storage::createLocalDriver(['root' => base_path()]);

        if ($client->exists('.gitignore')) {
            $gIgnore = file(base_path('.gitignore'));

            if ( ! in_array("/storage/$folder", array_map('trim', $gIgnore))) {
                $client->append('.gitignore', "/storage/$folder");
            }
        }
    }

}