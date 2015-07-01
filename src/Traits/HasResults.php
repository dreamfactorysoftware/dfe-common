<?php
namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for things that have results
 */
trait HasResults
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type mixed
     */
    protected $processResult = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->processResult;
    }

    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->processResult = $result;

        return $this;
    }
}