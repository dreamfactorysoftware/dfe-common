<!DOCTYPE html>
<html lang="en">
<head>
	@section('head-master')
		@include('layouts.partials.head-master')
	@show

	@section('head-links')
	@show

	@section('head-scripts')
	@show
</head>
<body class="@yield('body-class')">

<div id="page-content" class="container-fluid">
	@yield('content')
</div>

@section('before-body-scripts')
@show

@include('layouts.partials.body-scripts')

@section('after-body-scripts')
@show

{{-- @formatter:off --}}
@section('asset-path')
{{ __DIR__ . '/../../assets' }}
@stop
{{-- @formatter:on --}}
</body>
</html>
