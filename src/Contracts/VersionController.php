<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes an object that offers version control
 */
interface VersionController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    public function setRepository($path);

    public function checkout($revision);

    public function getRevisions();
}