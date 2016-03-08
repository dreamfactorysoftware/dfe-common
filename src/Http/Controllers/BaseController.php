<?php namespace DreamFactory\Enterprise\Common\Http\Controllers;

use DreamFactory\Enterprise\Common\Traits\Lumberjack;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

/**
 * The base DFE controller
 */
abstract class BaseController extends Controller
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Lumberjack;
}
