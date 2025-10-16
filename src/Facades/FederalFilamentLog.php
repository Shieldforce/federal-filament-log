<?php

namespace Shieldforce\FederalFilamentLog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Shieldforce\FederalFilamentLog\FederalFilamentLog
 */
class FederalFilamentLog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Shieldforce\FederalFilamentLog\FederalFilamentLog::class;
    }
}
