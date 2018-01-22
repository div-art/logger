<?php

namespace Divart\Logger\Http;

use App\Http\Controllers\Controller;
use Divart\Logger\Facades\Logger;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoggerController extends Controller{

	public function getLogData($data1 = '', $data2 = '')
	{
		$data = Logger::getAllLog();
		if (Logger::is_Date($data1)) {
			$data = Logger::searchByDate($data, $data1);
		} elseif ( !empty($data1)) {
			$data = ( !empty($data2)) ? Logger::searchByDate($data,$data2) : $data;
			$data = Logger::searchByType($data, $data1);
		}

		if ($data) {
			$logFile =  Logger::allLogFiles();
			$marks = Logger::getallMark(Logger::getAllLog());
			return View('logger::logger', compact('data','logFile','marks'));
		}

		return '<p>You dont have log</p><p>Return back? <a href="/logger">Yes</a><p>';
	}

	public function filterLogData(Request $request)
	{
		if ( !empty($request->all())) {
			$data = Logger::getAllLog();
			$data = ( !empty($request->date)) ? Logger::searchByDate($data, $request->date) : $data ;
			$data = ( !empty($request->mark)) ? Logger::searchByMark($data, $request->mark) : $data ;
			$data = ( !empty($request->type)) ? Logger::searchByType($data, $request->type) : $data ;
		}

		if ( !empty($data)){
			$logFile =  Logger::allLogFiles();
			$marks = Logger::getallMark(Logger::getAllLog());
			return View('logger::logger', compact('data','logFile','marks'));
		}

		return '<p>You dont have log</p><p>Return back? <a href="/logger">Yes</a><p>';
	}

	public function deleteAllLogFile()
	{
		Logger::deleteAllLogFile();
		return redirect()->back();
	}

	public function deleteOneLog(Request $request)
	{
		$data = $request->only('log');
		Logger::deleteOneLog($data['log']);

		return redirect()->back();
	}

	public function logDelete(Request $request)
	{
		if ( !empty($request->type) and !empty($request->datelog) or !empty($request->type) and !empty($request->filename)) {
			$filename = (!empty($request->datelog)) ? $request->datelog.'.json' : $request->filename;
			Logger::deleteLogByTypeInFindFile($request->type, $filename);
		} elseif ( !empty($request->filename)) {
			Logger::deleteDateLogFile($request->filename);
		} elseif ( !empty($request->datelog)) {
			Logger::deleteDateLogFile($request->datelog.'.json');
		} elseif ( !empty($request->type)) {
			Logger::deleteLogByType($request->type);
		}
		
		return redirect()->back();
	}
}