<?php namespace DreamFactory\Enterprise\Common\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

/**
 * Generic/standard authentication middleware
 * Designed for Laravel 5.1.x authentication
 */
class VerifyCsrfToken extends BaseVerifier
{
}
