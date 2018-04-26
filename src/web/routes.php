<?php

Route::group(['prefix' => 'div-art/logger'], function () {

    Route::get('/{type}/{date?}', 'DivArt\Logger\LogController@showLogs')
        ->where([
            'type' => '^(all)$',
            'date' => '^([0-9]{2}-[0-9]{2}-[0-9]{4})?$'
        ])
        ->name('all-logs');

    Route::post('filtered-logs', 'DivArt\Logger\LogController@filterLogs')
        ->name('filter-logs');

    Route::post('/log/delete', 'DivArt\Logger\LogController@deleteLog')
        ->name('delete-log');

    Route::post('/logfile/delete/{date}', 'DivArt\Logger\LogController@deleteLogFile')
        ->name('delete-log-file');

    Route::post('/logfile/delete-all-log-files', 'DivArt\Logger\LogController@deleteAllLogs')
        ->name('delete-all-log-files');

    Route::post('/logfile/delete-log-file-by-param', 'DivArt\Logger\LogController@deleteLogFileByParam')
        ->name('delete-log-file-by-param');
});