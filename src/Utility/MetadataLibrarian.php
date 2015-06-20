<?php namespace DreamFactory\Enterprise\Common\Utility;

class MetadataLibrarian extends Librarian
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type string The name of the metadata file. Override in your subclasses
     */
    const METADATA_FILE = 'metadata.json';

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function getArchiveFile()
    {
        return static::METADATA_FILE;
    }
}