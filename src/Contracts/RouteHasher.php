<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes a contract that hashes routes
 */
interface RouteHasher
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $pathToHash The path to hash
     * @param int    $keepDays   The number of days to keep the link active. Defaults to thirty (30) days. Setting $keepDays to zero (0) means "never
     *                           expire".
     *
     * @return string The hash/token representing the unique owner-path pair.
     */
    public function create($pathToHash, $keepDays = 30);

    /**
     * @param string $hashToResolve A hash generated by this object
     *
     * @return string Returns the path that belongs to the given hash
     * @throws \InvalidArgumentException when the owner-hash pair is invalid
     */
    public function resolve($hashToResolve);
}
