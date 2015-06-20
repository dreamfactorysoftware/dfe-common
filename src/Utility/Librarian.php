<?php namespace DreamFactory\Enterprise\Common\Utility;

use DreamFactory\Enterprise\Common\Contracts\Archivist;
use DreamFactory\Enterprise\Common\Contracts\Curated;
use DreamFactory\Enterprise\Common\Contracts\Custodial;
use DreamFactory\Enterprise\Common\Traits\Curator;
use DreamFactory\Enterprise\Common\Traits\Custodian;
use DreamFactory\Library\Utility\Json;
use League\Flysystem\Filesystem;

abstract class Librarian extends RestrictedCollection implements Custodial, Curated, Archivist
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Custodian, Curator;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Loads a collection from somewhere into itself. Prior contents are cleared
     *
     * @param \League\Flysystem\Filesystem $from     The source location to load
     * @param mixed|array                  $options  vendor-specific implementation options
     * @param string                       $activity The activity name to log
     *
     * @return bool
     */
    public function load($from, $options = [], $activity = null)
    {
        if (!($from instanceof Filesystem)) {
            throw new \InvalidArgumentException('Invalid "$from" file system specified.');
        }

        if ($from->has($_archiveFile = $this->getArchiveFile())) {
            $this->reset()->merge(Json::decode($from->read($_archiveFile), true));
            $this->addActivity($activity ?: 'load');

            return true;
        }

        return false;
    }

    /**
     * Save the collection somewhere
     *
     * @param mixed|\League\Flysystem\Filesystem $to       The destination
     * @param mixed|array                        $extras   additional information to save
     * @param string                             $activity The activity name to log
     *
     * @return bool
     */
    public function save($to, $extras = null, $activity = null)
    {
        if (!($to instanceof Filesystem)) {
            throw new \InvalidArgumentException('Invalid "$from" file system specified.');
        }

        if ($to->has($_archiveFile = $this->getArchiveFile()) && !$to->copy($_archiveFile, $_archiveFile . '.save')) {
            //  Just make a note of it...
            \Log::notice('[librarian] unable to save backup copy of archive "' . $_archiveFile . '"');
        }

        $this->addActivity($activity ?: 'save');

        return $to->put($_archiveFile, Json::encode(array_merge($this->toArray(), $extras ?: [])));
    }

    /**
     * Return the name of the library archive file
     *
     * @return string
     */
    abstract public function getArchiveFile();
}