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
            <p>This email is confirmation that the password on your DreamFactory Enterprise&trade; Dashboard has been changed. If you did not request this,
                please let us know.</p>
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
