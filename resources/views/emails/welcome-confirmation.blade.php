<?php
!isset($headTitle) && $headTitle = 'Welcome';
!isset($contentHeader) && $contentHeader = 'Welcome';
?>
@extends('dfe-common::layouts.responsive')
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
        <p>Your account on the DFE Console has been successfully created.</p>

        <p>In order to complete the registration process, please click the link below confirming your registered email address.</p>

        <p>
            <a href="{{ $confirmationUrl }}"
               title="Click to confirm your email address">{{ $confirmationUrl }}</a>
        </p>

        <p>
            If you've got any questions, feel free to drop us a line at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
        </p>

        <p>
            Have a great day!<br /> The Dream Team
        </p>
    </div>
@stop
