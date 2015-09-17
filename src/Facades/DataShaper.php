<?php namespace DreamFactory\Enterprise\Common\Facades;

use DreamFactory\Enterprise\Common\Providers\DataShaperServiceProvider;
use DreamFactory\Library\Utility\Enums\DataShapes;
use Illuminate\Support\Facades\Facade;

/**
 * DataShaper
 *
 * @method static array transform(array $data = [], $shape = DataShapes::JSON, array $options = []);
 */
class DataShaper extends Facade
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    protected static function getFacadeAccessor()
    {
        return DataShaperServiceProvider::IOC_NAME;
    }

}