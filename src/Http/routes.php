<?php

Route::group(['prefix' => 'logger', 'namespace' => 'Divart\Logger\Http'] , function(){

	Route::get('{data1?}/{data2?}', 'LoggerController@getLogData')->where(['data1'=>'[\w-]+'],['data2'=>'[\w-]+']);
	Route::post('/', 'LoggerController@filterLogData')->name('logger');
	Route::post('/logDelete/{logDelete?}', 'LoggerController@logDelete')->where('logDelete','[\w-]+')->name('logDelete');
	Route::post('/deleteOneLog', 'LoggerController@deleteOneLog')->name('deleteOneLog');
	Route::post('/deleteAllLogFile', 'LoggerController@deleteAllLogFile')->name('deleteAllLogFile');

});