@extends('dfe-common::layouts.common')

{{-- no spaces... it won't be trimmed --}}
{{-- @formatter:off --}}
@section('page-title'){{ "Registration Closed" }}@overwrite
{{-- @formatter:on --}}

@section('head-links')
    @parent
    <link href="/vendor/dfe-common/css/auth.css" rel="stylesheet">
@stop

@include('dfe-common::auth.branding',['pageDisplayName'=> config('dfe.common.display-name').': Registration Closed'])

@section('content')
    <div id="container-login" class="container-fluid">
        <div class="row">
            <div class="col-md-offset-3 col-md-6 col-md-offset-3 col-sm-offset-3 col-sm-6 col-sm-offset-3">
                <div class="container-logo">
                    <img src="/vendor/dfe-common/img/registration-closed.png">
                </div>

                <div class="jumbotron">
                    <h1>Sorry...</h1>

                    <p>Self-registration on this system is not allowed. Please contact your system's administrator for more information.</p>

                    <p><a class="btn btn-primary btn-lg" href="#" role="button">Login</a></p>
                </div>

            </div>
        </div>
    </div>
@stop

@section( 'after-body-scripts' )
    @parent
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
    <script src="/vendor/dfe-common/js/auth.validate.js"></script>
@stop
