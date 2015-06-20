<?php namespace DreamFactory\Enterprise\Common\Utility;

use DreamFactory\Enterprise\Services\Contracts\LibrarianAware;

class Canister implements LibrarianAware
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type Librarian
     */
    protected $librarian;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    public function __construct(Librarian $librarian)
    {
        $this->librarian = $librarian;
    }

    /**
     * @return Librarian
     */
    public function getLibrarian()
    {
        return $this->librarian;
    }
}