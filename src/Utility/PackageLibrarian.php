<?php namespace DreamFactory\Enterprise\Common\Utility;

class PackageLibrarian extends Librarian
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the package file
     */
    const PACKAGE_FILE = 'package.json';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function getArchiveFile()
    {
        return static::PACKAGE_FILE;
    }
}