# Logger
Logger package for Laravel

## Installation
To install, run the following in your project directory:

``` bash
$ composer require div-art/logger
```

Then in `config/app.php` add the following to the `providers` array:

```
\Divart\Logger\LoggerServiceProvider::class,
```

Also in config/app.php, add the Facade class to the aliases array:

```
'Logger' => \Divart\Logger\Facades\Logger::class,
```

## Configuration
To publish Logger's configuration file, run the following `vendor:publish` command:

```
php artisan vendor:publish --provider="Divart\Logger\LoggerServiceProvider"
```

## Usage
** Do not forget to include the namespace for the controller class where you plan to use this library **

```
use Divart\Logger\Facades\Logger;
```

## Add from ENV file:

```
#PATH_LOG - path where been save logfile, default value 'storage/logger'
PATH_LOG=storage/logger

#LOG_SAVE - time saving your logfile, defaul value 7 days
LOG_SAVE=7
```

## Example:

``` php
Logger::save(array('name' => 'sani', 'year' => '30'));
```

Create logfile in default path 'storage/logger' whith name date.json

date.json:

{
	'name' => 'sani',
	'year' => '30'
}

## Route:

``` php
//get all log
Route::get('/logger');

//get all log by date
Route::get('/logger/{date?}');

//get all log by type
Route::get('/logger/{type}');

//get all log by type and date
Route::get('/logger/{type}/{date?}');
```

## Methods:

``` php
Logger::save(data, ['mark']);
Logger::info(data, ['mark']);
Logger::danger(data, ['mark']);
Logger::success(data, ['mark']);

//data - data (line, number, boolean value, array, object)
//mark - text mark for filtering logs, optional parameter

Logger::request(['key']);
Logger::input(['key']);
Logger::json(['key']);
Logger::post(['key']);
Logger::get(['key']);
Logger::php(['key']);
Logger::server(['key']);
Logger::cookies(['key']);
Logger::headers(['key']);

//key - is an optional parameter that specifies a particular key from an array or object to be written
```

## License
The MIT License (MIT). Please see License File for more information."# Logger" 
"# Logger" 
