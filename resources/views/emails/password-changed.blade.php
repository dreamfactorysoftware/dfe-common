@extends('dfe-common::layouts.responsive')
<?php
!isset($headTitle) && $headTitle = 'Password Changed';
!isset($contentHeader) && $contentHeader = 'Password Changed';
?>
{{--
 This blade is for generating passwor reset emails.
--}}
@section('contentBody')
    <div>
        <p>This email is confirmation that the password on your DreamFactory Enterprise&trade; Dashboard has been changed. If you did not request this,
            please let us know.</p>
    </div>
@stop
