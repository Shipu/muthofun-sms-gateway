<?php

namespace Shipu\MuthoFun\Facades;

use Illuminate\Support\Facades\Facade;

class MuthoFun extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'muthofun';
    }
}
