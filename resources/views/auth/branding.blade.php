@section('auth.branding')
    <!-- Branding for login page -->
	<h3><img src="{{ config('dfe.common.login-splash-image') }}" alt="" />
		<small>{{ $pageDisplayName or config('dfe.common.display-name') }}
			<span>{{ config('dfe.common.display-version') }}</span>
		</small>
	</h3>
@overwrite
