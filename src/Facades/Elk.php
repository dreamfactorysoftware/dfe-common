<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Enums\ElkIntervals;
use DreamFactory\Enterprise\Common\Providers\ElkServiceProvider;
use Elastica\Client;
use Elastica\ResultSet;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ResultSet callOverTime($facility, $interval = ElkIntervals::DAY, $size = 30, $from = 0, $term = null)
 * @method static bool|array globalStats($from = 0, $size = 1)
 * @method static array allStats($from = null, $size = null)
 * @method static ResultSet termQuery($term, $value, $size = 30)
 * @method static Client getClient();
 */
class Elk extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ElkServiceProvider::IOC_NAME;
    }
}
