<?php
!isset($headTitle) && $headTitle = 'Password Reset';
!isset($contentHeader) && $contentHeader = 'Password Reset';
?>
@extends('dfe-common::layouts.responsive')
{{--
 This blade is for generating passwor reset emails.
--}}
@section('contentBody')
    <div>
        <p>We have received a request to reset your password.</p>
        <p>Please click the below link to complete the reset process. You will be taken to the DreamFactory Enterprise&trade; Dashboard where you will be
            prompted to enter and confirm a new password.</p>
        <p><a href="{{ url('password/reset/'.$token) }}" target="_blank">{{ url('password/reset/'.$token) }}</a></p>
        <p>Your reset link will expire in 1 day and may only be used a single time.</p>
    </div>
@stop
