<?php

namespace Divart\Logger;

use Illuminate\Support\ServiceProvider;

class FileManager
{
    function __construct()
    {
        $this->root = '../';
        $this->path = env('PATH_LOG', 'storage/logger');
        $this->day = env('LOG_SAVE', '7');
    }

    public function allLogFiles()
    {
        $this->deleteLogFile();
        return array_except(scandir($this->root.$this->path), [0,1,2]);
    }

    public function getallMark($data)
    {
        for ($i = 0, $marks = []; $i < count($data); $i++) {
            if ( !empty($data[$i]->mark)) array_push($marks, $data[$i]->mark);
        }
        return array_unique($marks);
    }

    public function searchByDate($data, $date)
    {
        for ($i = 0; $i < count($data); $i++) {
            if ( mb_substr($data[$i]->time, 0, 10) == $date) $log[] = $data[$i];
        }
        if ( !empty($log)) return $log;
    }

    public function searchByType($data, $type)
    {
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]->type == $type) $log[] = $data[$i];
        }
        if ( !empty($log)) return $log;
    }

    public function searchByMark($data, $mark)
    {
        for ($i = 0; $i < count($data); $i++){
            if ($data[$i]->mark == $mark) $log[] = $data[$i];
        }
        if ( !empty($log)) return $log;
    }

    public function is_Date($str)
    {
        return is_numeric(strtotime($str));
    }

    public function gitignoreLogFile()
    {
        $text = "*\r\n!.gitignore";
        return file_put_contents($this->root.$this->path.'/.gitignore', $text);
    }

    public function createFolder()
    {
        $this->path = ($this->path[0] === '/') ? substr($this->path, 1) : $this->path;
        if ( !is_dir($this->root.$this->path)) mkdir($this->root.$this->path, 0777, true);

        return $this->gitignoreLogFile();
    }

    public function saveDataInLogFile($data, $filename = NULL)
    {
        $filename = ( is_null($filename)) ? date('Y-m-d').".json" : $filename;
        return file_put_contents($this->root.$this->path.'/'.$filename, json_encode($data)."\n", FILE_APPEND);
    }

    public function getAllLog()
    {
        $this->createFolder();
        if ($this->allLogFiles()) {

            foreach ($this->allLogFiles() as $value) {
                $log_data[] = file($this->root.$this->path.'/'.$value);
            }

            for ($i = 0, $logs = []; $i < count($log_data); $i++) {
                $logs = array_merge($logs, $log_data[$i]);
            }

            for ($i = 0; $i < count($logs); $i++) {
                $logs[$i] = json_decode($logs[$i]);
            }

            return $logs;
        }  
    }

    public function deleteAllLogFile()
    {
        foreach ($this->allLogFiles() as $value) unlink ($this->root.$this->path.'/'.$value);
    }

    public function deleteDateLogFile($filename)
    {
        foreach ($this->allLogFiles() as $value) {
            if ($value == $filename) unlink($this->root.$this->path.'/'.$filename);
        }
    }

    public function deleteLogFile()
    {
        if ( file_exists($this->root.$this->path.'/'.date('Y-m-d', time()-$this->day*60*60*24).'.json')) {
            return unlink($this->root.$this->path.'/'.date('Y-m-d', time()-$this->day*60*60*24).'.json');
        }
    }

    public function deleteLogByType($type)
    {
        foreach ($this->allLogFiles() as $value) {

            $logfile = file($this->root.$this->path.'/'.$value);
            unlink($this->root.$this->path.'/'.$value);

            foreach ($logfile as $k => $v) {
                $logfile[$k] = json_decode($logfile[$k]);
                if ( $logfile[$k]->type != $type) $this->saveDataInLogFile($logfile[$k], $value);
            }
        }
    }

    public function deleteLogByTypeInFindFile($type, $filename)
    {
        $logfile = file($this->root.$this->path.'/'.$filename);
        unlink($this->root.$this->path.'/'.$filename);

        foreach ($logfile as $k => $v) {
            $logfile[$k] = json_decode($logfile[$k]);
            if ( $logfile[$k]->type != $type) $this->saveDataInLogFile($logfile[$k], $filename);
        }
    }

    public function deleteOneLog($log)
    {
        $filename = mb_substr($log, 0, 10).'.json';
        $logfile = file($this->root.$this->path.'/'.$filename);
        unlink($this->root.$this->path.'/'.$filename);

        foreach ($logfile as $k => $v) {
            $logfile[$k] = json_decode($logfile[$k]);
            if ( $logfile[$k]->time != $log) $this->saveDataInLogFile($logfile[$k], $filename);
        }
    }
}