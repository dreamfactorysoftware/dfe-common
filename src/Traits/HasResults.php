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
    protected $_result = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @param mixed $result
     *
     * @return $this
     */
    public function setResult( $result )
    {
        $this->_result = $result;

        return $this;
    }
}