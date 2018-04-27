# Logger
Logger package for Laravel

## Installation
To install, run the following in your project directory:

``` bash
$ composer require div-art/logger
```

If you are using laravel version >= 5.5 you can skip this step:

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

It will create `logger.php` file in `config/logger.php`.

To manage directory for storing logs just change default value of `'path'`:

```
//the root is storage directory
'path' => 'your path',
```

By default logs lifetime is `7` days, to change it:

```
//this integer value must be more than 0
'expire_days' => 7
```

## Usage
** Do not forget to include the namespace for the controller class where you plan to use this library **

```
use DivArt\Logger\Facades\Logger;
```

But if you are using laravel >= 5.5 you can skip this.

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
Route::get('/div-art/logger/all/{date?}');
```

To manage all your logs go to this link example in browser `http://your-app.xyz/div-art/logger/all`

## Methods:

``` php
Logger::save(data, 'mark');
Logger::info(data, 'mark');
Logger::danger(data, 'mark');
Logger::success(data, 'mark');

//data - data (string, number, boolean value, array, object), required parameter
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
