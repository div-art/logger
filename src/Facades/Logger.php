<?php

namespace Divart\Logger\Facades;

use Illuminate\Support\Facades\Facade;

Class Logger extends Facade{

	protected static function getFacadeAccessor()
	{
		return 'logger';
	}
}