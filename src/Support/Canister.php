<?php namespace DreamFactory\Enterprise\Common\Support;

use DreamFactory\Enterprise\Services\Contracts\LibrarianAware;
use League\Flysystem\Filesystem;

class Canister implements LibrarianAware
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /** @inheritdoc */
    const COMPRESSION_TYPE = 'zip';

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Librarian
     */
    protected $librarian;
    /**
     * @type string The relative name of the canister file
     */
    protected $filename;
    /**
     * @type Filesystem The filesystem in which $filename lives
     */
    protected $filesystem;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string                                                 $filename   The name of canister file
     * @param \League\Flysystem\Filesystem                           $filesystem The filesystem for $filename
     * @param \DreamFactory\Enterprise\Common\Utility\Librarian|null $librarian  Any librarian for this canister
     */
    public function __construct($filename, Filesystem $filesystem, Librarian $librarian = null)
    {
        $this->filesystem = $filesystem;
        $this->filename = $filename;
        $this->librarian = $librarian;
    }

    public function addParcel($parcel)
    {

    }

    /**
     * @return Librarian
     */
    public function getLibrarian()
    {
        return $this->librarian;
    }
}