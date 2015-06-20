<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Something that stores collections. Mainly to disk
 */
interface Archivist
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Load a collection from somewhere
     *
     * @param mixed|\League\Flysystem\Filesystem $from     The source location to load
     * @param mixed|array                        $options  vendor-specific implementation options
     * @param string                             $activity The activity name to log
     *
     * @return bool
     */
    public function load($from, $options = [], $activity = null);

    /**
     * Save the collection somewhere
     *
     * @param mixed|\League\Flysystem\Filesystem $to       The destination
     * @param mixed|array                        $extras   additional information to save
     * @param string                             $activity The activity name to log
     *
     * @return bool
     */
    public function save($to, $extras = null, $activity = null);

}