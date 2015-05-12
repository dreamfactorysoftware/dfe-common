<?php namespace DreamFactory\Enterprise\Common\Traits;

trait CustomLogPath
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @param string $fromClass The class to replace
     * @param string $toClass   The replacement
     */
    protected function _replaceLoggingConfigurationClass( $fromClass, $toClass )
    {
        if ( empty( $fromClass ) || empty( $toClass ) )
        {
            throw new \InvalidArgumentException( 'Neither $fromClass or $toClass may be blank.' );
        }

        $_straps = array_flip( $this->bootstrappers );

        if ( array_key_exists( $fromClass, $_straps ) )
        {
            $this->bootstrappers[$_straps[$fromClass]] = $toClass;
        }
    }
}