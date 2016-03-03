@extends('dfe-common::emails.responsive')
{{--

 This blade is for generating passwor reset emails.

 The following view data is expected:

 $headTitle           The title of the email/page
 $contentHeader       The callout/header of the email's body
 $firstName           The first name of the recipient
 $instanceName        The name of the instance
 $instanceUrl         The instance's URL
 $emailBody           The optional guts of the email. Will be placed in its own div

 Provided to all views

 $dashboard_url             The dashboard's URL
 $support_email_address     The support email address

--}}
@section('contentBody')
    <div>
        <p>
            {{ $firstName }},
        </p>

        <div>
            <p>You have requested that your password be reset. Please click the below link to complete the reset process. You will be taken to the DreamFactory
                Enterprise&trade; Dashboard where you will be prompted to enter and confirm a new password.</p>
            <p><a href="{{ url('password/reset/'.$token) }}" target="_blank">{{ url('password/reset/'.$token) }}</a></p>
            <p>Your reset link will expire in 1 day and may only be used a single time.</p>
        </div>

        @if(isset($emailBody))
            <div>{!! $emailBody !!}</div>
        @endif

        <div>
            <p>Go to your DreamFactory&trade; Dashboard at <a href="{{ $dashboard_url }}" target="_blank">{{ $dashboard_url }}</a> to create a new instance, or
                manage your other instances.</p>
        </div>

        <p>
            Thanks!
            <cite>-- Team DreamFactory</cite>
        </p>
    </div>
@stop
