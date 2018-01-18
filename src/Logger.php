<?php


namespace Divart\Logger;

use Illuminate\Support\ServiceProvider;

class Logger{


    public static function save($data, $mark = ''){

        $mass = array(
            'time' => date('Y-m-d-H-i-s'),
            'type' => 'save',
            'mark' => $mark,
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function info($data, $mark = ''){

        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'info',
            'mark' => $mark,
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function danger($data, $mark = ''){

        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'danger',
            'mark' => $mark,
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function success($data, $mark = ''){

        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'success',
            'mark' => $mark,
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function request($key = NULL){

        (is_null($key)) ? $data = request() : $data = request()->only($key);
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'request',
            'mark' => 'request',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function input($key = NULL){

        (is_null($key)) ? $data = request()->all() : $data = request()->all($key);
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'input',
            'mark' => 'input',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function json($key = NULL){

        (is_null($key)) ? $data = request()->json()->all() : $data = request()->json()->all($key);
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'json',
            'mark' => 'json',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function post($key = NULL){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            (is_null($key)) ? $data = $_REQUEST : $data[$key] = $_REQUEST[$key];
                $mass = array(
                'time' => date('Y-m-d-G-i-s'),
                'type' => 'post',
                'mark' => 'post',
                'data' => $data
            );

        Logger::saveDataInLogFile($mass);
        }

    }

    public static function get($key = NULL){

        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            (is_null($key)) ? $data = $_REQUEST : $data[$key] = $_REQUEST[$key];
                $mass = array(
                'time' => date('Y-m-d-G-i-s'),
                'type' => 'get',
                'mark' => 'get',
                'data' => $data
            );

        Logger::saveDataInLogFile($mass);
        }
    }

    public static function php($key = NULL){

        (is_null($key)) ? $data = $_REQUEST : $data[$key] = $_REQUEST[$key];
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'php://input',
            'mark' => 'php://input',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function server($key = NULL){
        
        (is_null($key)) ? $data = $_SERVER : $data[$key] = $_SERVER[$key];
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'server',
            'mark' => 'server',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function cookies($key = NULL){

        (is_null($key)) ? $data = $_COOKIE : $data[$key] = $_COOKIE[$key];
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'cookies',
            'mark' => 'cookies',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function headers($key = NULL){

        (is_null($key)) ? $data = header() : $data[$key] = request()->header($key);
        $mass = array(
            'time' => date('Y-m-d-G-i-s'),
            'type' => 'headers',
            'mark' => 'headers',
            'data' => $data
        );

        Logger::saveDataInLogFile($mass);
    }

    public static function allLogFiles(){

        $path = env('PATH_LOG', 'storage/logger');
        if($path === 'storage/logger'){
            $file = scandir(storage_path('logger'));
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);

        }else{
            $file = scandir($path);
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);
        }
        return $file;
    }

    public static function getallMark($data){

        $data = json_decode($data);
        $marks = [];
        foreach ($data as $key => $value) {
            if(!empty($data[$key]->mark)){
                array_push($marks, $data[$key]->mark);
            }
        }
        $marks = array_unique($marks);
        return $marks;

    }

    public static function searchByDate($data, $date){

        $databydate = [];
        foreach ($data as $key => $value) {
            if(mb_substr($data[$key]->time, 0, 10) == $date){
                $databydate[] = $data[$key];
            }
        }
        return $databydate;
    }

    public static function searchByType($data, $type){

        $databytype = [];
        foreach ($data as $key => $value) {
            if($data[$key]->type == $type){
                $databytype[] = $data[$key];
            }
        }
        return $databytype;
    }

    public static function searchByMark($data, $mark){

        $databymark = [];
        foreach ($data as $key => $value) {
            if($data[$key]->mark == $mark){
                $databymark[] = $data[$key];
            }
        }
        return $databymark;
    }

    public static function is_Date($str){
        return is_numeric(strtotime($str));
    }

    public static function gitignoreLogFile($path){

        $text = "*\r\n!.gitignore";
        file_put_contents($path.'/.gitignore', $text);
    }


    public static function createFolder($path){

        $path = ($path[0] === '/') ? substr($path, 1) : $path;

        if($path === 'storage/logger'){
            $full_path = storage_path('logger');
            if(!is_dir($full_path)) mkdir($full_path, 0755);
        }else{

            $array_dir = explode('/', $path);
            $full_path = '';
            for ($i=0; $i < count($array_dir); $i++) {
                $full_path .= $array_dir[$i];
                if(!file_exists($full_path)) mkdir($full_path, 0755);
                $full_path .= '/';
            }
        }

        Logger::gitignoreLogFile($full_path);
    }

    public static function deleteAllLogFile(){

        $path = env('PATH_LOG', 'storage/logger');

        if($path == 'storage/logger'){
            $file = scandir(storage_path('logger'));
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);

            foreach ($file as $value) {
                unlink(storage_path('logger/'.$value));
            }

        }else{
            $file = scandir($path);
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);

            foreach ($file as $value) {
                unlink($path.'/'.$value);
            }
        }
    }

    public static function deleteDateLogFile($filename){

        $path = env('PATH_LOG', 'storage/logger');
        if($path == 'storage/logger'){

            $file = scandir(storage_path('logger'));
            foreach ($file as $value) {
                if($value == $filename){
                    return unlink(storage_path('logger/'.$filename));
                }
            }

        }else{

            $file = scandir($path);
            foreach ($file as $value) {
                if($value == $filename){
                    return unlink($path.'/'.$filename);
                }
            }
        }
    }

    public static function deleteLogFileByType($type){

        $path = env('PATH_LOG', 'storage/logger');
        if($path == 'storage/logger'){

            $file = scandir(storage_path('logger'));
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);
            foreach ($file as $value) {
                $logfile = file_get_contents(storage_path('logger/'.$value));
                $logfile = json_decode('['.$logfile.']');

                foreach ($logfile as $key => $v) {

                    if($logfile[$key]->type == $type){
                        unset($logfile[$key]);
                    }else{

                        if(file_exists('test.json')){
                            $tempdata = file_get_contents('test.json');
                            $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                            file_put_contents('test.json', $tempdata);
                        }else{
                            $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                            file_put_contents('test.json', $da);
                        }  
                    }
                }
                if(file_exists('test.json')){
                    $data = file_get_contents('test.json');
                    file_put_contents(storage_path('logger/'.$value), $data);
                }else{
                    unlink(storage_path('logger/'.$value));
                }
            }

        }else{

            $file = scandir($path);
            unset($file[0]);
            unset($file[1]);
            unset($file[2]);
            foreach ($file as $value) {
                $logfile = file_get_contents($path.'/'.$value);
                $logfile = json_decode('['.$logfile.']');

                foreach ($logfile as $key => $v) {

                    if($logfile[$key]->type == $type){
                        unset($logfile[$key]);
                    }else{

                        if(file_exists('test.json')){
                            $tempdata = file_get_contents('test.json');
                            $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                            file_put_contents('test.json', $tempdata);
                        }else{

                            $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                            file_put_contents('test.json', $da);
                        }  
                    }
                }
                if(file_exists('test.json')){
                    $data = file_get_contents('test.json');
                    file_put_contents($path.'/'.$value,$data);
                }else{
                    unlink($path.'/'.$value);
                }
            }
        }

        if(file_exists('test.json')) unlink('test.json');

    }

    public static function deleteLogFileByTypeInFindFile($type,$filename){
        
        $path = env('PATH_LOG', 'storage/logger');


        if($path == 'storage/logger'){

            $file = scandir(storage_path('logger'));
            foreach ($file as $value) {
                if($value == $filename){
                    $logfile = file_get_contents(storage_path('logger/'.$filename));
                    $logfile = json_decode('['.$logfile.']');

                    foreach ($logfile as $key => $value) {

                        if($logfile[$key]->type == $type){
                            unset($logfile[$key]);
                        }else{

                            if(file_exists('test.json')){
                                $tempdata = file_get_contents('test.json');
                                $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $tempdata);
                            }else{

                                $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $da);
                            }  
                        }
                    }             
                }
            }
            if(file_exists('test.json')){
                $data = file_get_contents('test.json');
                file_put_contents(storage_path('logger/'.$filename),$data);
            }else{
                (file_exists(storage_path('logger/'.$filename))) ? unlink(storage_path('logger/'.$filename)) : '';
            }

        }else{

            $file = scandir($path);
            foreach ($file as $value) {
                if($value == $filename){
                    $logfile = file_get_contents($path.'/'.$filename);
                    $logfile = json_decode('['.$logfile.']');

                    foreach ($logfile as $key => $value) {

                        if($logfile[$key]->type == $type){
                            unset($logfile[$key]);
                        }else{

                            if(file_exists('test.json')){
                                $tempdata = file_get_contents('test.json');
                                $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $tempdata);
                            }else{
                                $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $da);
                            }  
                        }
                    }             
                }
            }
            if(file_exists('test.json')){
                $data = file_get_contents('test.json');
                file_put_contents($path.'/'.$filename,$data);
            }else{
                (file_exists($path.'/'.$filename)) ? unlink($path.'/'.$filename) : '';
            }
        }

        if(file_exists('test.json')) unlink('test.json');

    }

    public static function deleteOneLog($log){

        $path = env('PATH_LOG', 'storage/logger');
        $filename = mb_substr($log, 0, 10).'.json';

        if($path == 'storage/logger'){

            $file = scandir(storage_path('logger'));
            foreach ($file as $value) {
                if($value == $filename){
                    $logfile = file_get_contents(storage_path('logger/'.$filename));
                    $logfile = json_decode('['.$logfile.']');

                    foreach ($logfile as $key => $value) {

                        if($logfile[$key]->time == $log){
                            unset($logfile[$key]);
                        }else{

                            if(file_exists('test.json')){
                                $tempdata = file_get_contents('test.json');
                                $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $tempdata);
                            }else{

                                $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $da);
                            }  
                        }
                    }             
                }
            }
            if(file_exists('test.json')){
                $data = file_get_contents('test.json');
                file_put_contents(storage_path('logger/'.$filename),$data);
            }else{
                unlink(storage_path('logger/'.$filename));
            }

        }else{

            $file = scandir($path);
            foreach ($file as $value) {
                if($value == $filename){
                    $logfile = file_get_contents($path.'/'.$filename);
                    $logfile = json_decode('['.$logfile.']');

                    foreach ($logfile as $key => $value) {

                        if($logfile[$key]->time == $log){
                            unset($logfile[$key]);
                        }else{

                            if(file_exists('test.json')){
                                $tempdata = file_get_contents('test.json');
                                $tempdata = $tempdata.",\r\n".json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $tempdata);
                            }else{
                                $da = json_encode($logfile[$key], JSON_PRETTY_PRINT);
                                file_put_contents('test.json', $da);
                            }  
                        }
                    }             
                }
            }
            if(file_exists('test.json')){
                $data = file_get_contents('test.json');
                file_put_contents($path.'/'.$filename,$data);
            }else{
                unlink($path.'/'.$filename);
            }
        }

        if(file_exists('test.json')) unlink('test.json');
    }

    public static function deleteLogFile(){

        $path = env('PATH_LOG', 'storage/logger');

        $dataLogDelete = date('Y-m-d', env('LOG_SAVE', 7)*60*60*24);

        if($path === 'storage/logger'){

            $file = scandir(storage_path('logger'));
            foreach ($file as $value) {
                ($value == $dataLogDelete.'.json') ? unlink($dataLogDelete.'.json') : '';
            }

        }else{

            $file = scandir($path);
            foreach ($file as $value) {
                ($value == $dataLogDelete.'.json') ? unlink($dataLogDelete.'.json') : '';
            }
        }

        return $dataLogDelete;
    }

    public static function saveDataInLogFile($data){

        Logger::createFolder(env('PATH_LOG', 'storage/logger'));

        $path = env('PATH_LOG', 'storage/logger');

        $file_name = date('Y-m-d').".json";
        $json_data = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents('temp.json', $json_data);
        $data = file_get_contents('temp.json');
        unlink('temp.json');

        if($path === 'storage/logger'){

            if(file_exists(storage_path('logger/'.$file_name))){
                $fp=fopen(storage_path('logger/'.$file_name),"a");
                fwrite($fp, ",\r\n" . $data);
            }else{
                $fp=fopen(storage_path('logger/'.$file_name),"a");
                fwrite($fp, $data);
            }

        }else{

            if(file_exists($path.'/'.$ame)){
                $fp=fopen($path.'/'.$file_name,"a");
                fwrite($fp, ",\r\n" . $data);
            }else{
                $fp=fopen($path.'/'.$file_name,"a");
                fwrite($fp, $data);
            }
        }
        fclose($fp);
    }

    public static function getAllLog(){

        $path = env('PATH_LOG', 'storage/logger');

        if($path === 'storage/logger'){
            $file = scandir(storage_path('logger'));
            unset($file[0]);
            unset($file[1]);

            foreach ($file as $value) {

                if($value != '.gitignore'){

                    $log_data = file_get_contents(storage_path("logger/$value"));
                    if(file_exists("test.json")){
                        $fp=fopen("test.json","a");
                        fwrite($fp, ",\r\n" . $log_data); 
                    }else{
                        $fp=fopen("test.json","a");
                        fwrite($fp, $log_data);
                    }
                    fclose($fp);
                }
            }

        }else{
            $file = scandir($path);
            unset($file[0]);
            unset($file[1]);

            foreach ($file as $value) {

                if($value != '.gitignore'){

                    $log_data = file_get_contents($path."/".$value);
                    if(file_exists("test.json")){
                        $fp=fopen("test.json","a");
                        fwrite($fp, ",\r\n" . $log_data); 
                    }else{
                        $fp=fopen("test.json","a");
                        fwrite($fp, $log_data);
                    }
                    fclose($fp);
                }
            }
        }

        if(file_exists('test.json')){
            $text = file_get_contents('test.json'); 
            $fd = '['.$text.']'; 
            $f_out = fopen("test.json","w"); 
            fwrite($f_out, $fd);  
            fclose($f_out);

            if(file_exists('test.json')){
                $data = file_get_contents('test.json');
                unlink('test.json');
            }

            return $data;
        }
    }


    public static function getLogType($array, $search){

        $array = json_decode($array);
        foreach($array as $key => $value) {

            if($array[$key]->type == $search){

                if(file_exists("test.json")){

                    $array[$key] = json_encode($array[$key]);
                    $fp=fopen("test.json","a");
                    fwrite($fp, ",\r\n" .  $array[$key]);

                }else{

                    $array[$key] = json_encode($array[$key]);
                    $fp=fopen("test.json","a");
                    fwrite($fp,  $array[$key]); 
                }
                fclose($fp);
            }
        }

        $text = file_get_contents('test.json'); 
        $fd = '['.$text.']'; 
        $f_out = fopen("test.json","w"); 
        fwrite($f_out, $fd);  
        fclose($f_out);

        if(file_exists('test.json')){
            $data = file_get_contents('test.json');
            unlink('test.json');
        }
        return $data;        

    }

    public static function getLogData($array, $search){

        $path = env('PATH_LOG', 'storage/logger');
        $search = $search.'.json';

        if($path === 'storage/logger'){
            $file = scandir(storage_path('logger'));
            unset($file[0]);
            unset($file[1]);

            foreach ($file as $value) {

                if($value == $search){

                    $log_data = file_get_contents(storage_path("logger/$value"));
                    if(file_exists("test.json")){
                        $fp=fopen("test.json","a");
                        fwrite($fp, ",\r\n" . $log_data);

                    }else{
                        $fp=fopen("test.json","a");
                        fwrite($fp, $log_data); 
                    }
                    fclose($fp);
                }
            }

        }else{
            $file = scandir($path);
            unset($file[0]);
            unset($file[1]);

            foreach ($file as $value) {

                if($value == $search){

                    $log_data = file_get_contents($path."/".$value);
                    if(file_exists("test.json")){
                        $fp=fopen("test.json","a");
                        fwrite($fp, ",\r\n" . $log_data); 

                    }else{
                        $fp=fopen("test.json","a");
                        fwrite($fp, $log_data);
                    }
                    fclose($fp);
                }
            }
        }

        if(file_exists('test.json')){
            $text = file_get_contents('test.json'); 
            $fd = '['.$text.']'; 
            $f_out = fopen("test.json","w"); 
            fwrite($f_out, $fd);  
            fclose($f_out);
        }else{
            return redirect('/logger');
        }

        if(file_exists('test.json')){
            $data = file_get_contents('test.json');
            unlink('test.json');
        }
        return $data;
    }

}