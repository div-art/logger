<?php


namespace Divart\Logger\Http;

use App\Http\Controllers\Controller;
use Divart\Logger\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoggerController extends Controller{

	public function getLogData($data1 = '', $data2 = ''){

		Logger::createFolder(env('PATH_LOG', 'storage/logger'));
		Logger::deleteLogFile();
		$logFile =  Logger::allLogFiles();
		$data = Logger::getAllLog();
		
		if(Logger::is_Date($data1)){

			$data = Logger::getLogData($data, $data1);

		}elseif(!empty($data1)){

			$data = (!empty($data2)) ? Logger::getLogData($data,$data2) : $data;
			$data = Logger::getLogType($data, $data1);
		}

		$data = json_decode($data);

		for ($i=0; $i < count($data); $i++) { 
			$data[$i] = (array)$data[$i];
			$data[$i]['data'] = (array)$data[$i]['data'];
		}

		if(!empty($data)){
			$marks = Logger::getallMark(Logger::getAllLog());
			return View('logger::logger', compact('data','logFile','marks'));
		}else{
			return '<p>You dont have log</p>';
		}

	}

	public function filterLogData(Request $request){

		Logger::deleteLogFile();
		$logFile =  Logger::allLogFiles();

		if(!empty($request->all())){
			$data = json_decode(Logger::getAllLog());
			$data = (!empty($request->date)) ? Logger::searchByDate($data, $request->date) : $data ;
			$data = (!empty($request->mark)) ? Logger::searchByMark($data, $request->mark) : $data ;
			$data = (!empty($request->type)) ? Logger::searchByType($data, $request->type) : $data ;
		}

		$marks = Logger::getallMark(Logger::getAllLog());

		for ($i=0; $i < count($data); $i++) { 
			$data[$i] = (array)$data[$i];
			$data[$i]['data'] = (array)$data[$i]['data'];
		}

		if(!empty($data)){
			return View('logger::logger', compact('data','logFile','marks'));
		}else{
			return '<p>You dont have log</p><p>Return back? <a href="/logger">Yes</a><p>';
		}

	}


	public function deleteAllLogFile(){

		Logger::deleteAllLogFile();
		return redirect()->back();
	}

	public function deleteOneLog(Request $request){

		$data = $request->only('log');
		Logger::deleteOneLog($data['log']);
		return redirect()->back();
	}

	public function logDelete(Request $request){

		if(!empty($request->type) and !empty($request->datelog) or !empty($request->type) and !empty($request->filename)){

			$filename = (!empty($request->datelog)) ? $request->datelog.'.json' : $request->filename;
			Logger::deleteLogFileByTypeInFindFile($request->type, $filename);

		}elseif(!empty($request->filename)){

			Logger::deleteDateLogFile($request->filename);

		}elseif(!empty($request->datelog)){

			Logger::deleteDateLogFile($request->datelog.'.json');

		}elseif(!empty($request->type)){

			Logger::deleteLogFileByType($request->type);
		}
		
		return redirect()->back();
	}

}