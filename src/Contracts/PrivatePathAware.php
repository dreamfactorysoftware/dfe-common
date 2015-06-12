<?php
namespace DreamFactory\Enterprise\Common\Contracts;

use DreamFactory\Enterprise\Database\Models\Instance;

/**
 * Something that is aware of private paths
 */
interface PrivatePathAware
{
    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * @param string $append
     *
     * @return string
     */
    public function getPrivatePath($append = null);

    /**
     * @param string $append
     *
     * @return string
     */
    public function getOwnerPrivatePath($append = null);

    /**
     * Create a hash that will serve as the root directory for this user's space
     *
     * @param \DreamFactory\Enterprise\Database\Models\Instance $instance
     *
     * @return string
     */
    public function getOwnerHash(Instance $instance);

    /**
     * Returns an array of storage path segment mappings. i.e. ['zone'=>'xyz', 'partition' => 'abc', 'root-hash' => 'hash']
     *
     * @return array
     */
    public function getStorageMap();
}