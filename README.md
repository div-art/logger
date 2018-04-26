# Logger
Logger package for Laravel

## Installation
To install, run the following in your project directory:

``` bash
$ composer require div-art/logger
```

If you are using laravel > 5.5 you can skip this step:

In `config/app.php` add the following to the `providers` array:

```
\DivArt\Logger\LoggerServiceProvider::class,
```

Also in config/app.php, add the Facade class to the aliases array:

```
'Logger' => \DivArt\Logger\Facades\Logger::class,
```

## Configuration
To publish Logger's configuration file, run the following `vendor:publish` command:

```
php artisan vendor:publish --provider="DivArt\Logger\LoggerServiceProvider"
```

## Usage
** Do not forget to include the namespace for the controller class where you plan to use this library **

```
use DivArt\Logger\Facades\Logger;
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
//get all logs
Route::get('/logger/all/{date?}');
```

## Methods:

``` php
Logger::save(data, 'mark');
Logger::info(data, 'mark');
Logger::danger(data, 'mark');
Logger::success(data, 'mark');

//data - data (string, number, boolean value, array, object)
//mark - string mark for filtering logs, optional parameter

Logger::request('key');
Logger::input('key');
Logger::json('key');
Logger::post('key');
Logger::get('key');
Logger::php('key');
Logger::server('key');
Logger::cookies('key');
Logger::headers('key');

//key - is an optional parameter that specifies a particular key from an array or object to be written
```

## License
The MIT License (MIT). Please see License File for more information."# Logger" 
"# Logger" 
