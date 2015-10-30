<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\ScalpelServiceProvider;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string make($templateKey, array $data = [], array $mergeData = [])
 * @method static string makeFromString($template, array $data = [], array $mergeData = [])
 */
class Scalpel extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ScalpelServiceProvider::IOC_NAME;
    }
}
