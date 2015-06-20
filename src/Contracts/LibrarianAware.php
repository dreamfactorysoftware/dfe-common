<?php namespace DreamFactory\Enterprise\Services\Contracts;

use DreamFactory\Enterprise\Common\Utility\Librarian;

/**
 * something that manages a librarian
 */
interface LibrarianAware
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return Librarian
     */
    public function getLibrarian();
}