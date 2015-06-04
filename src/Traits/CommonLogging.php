<?php namespace DreamFactory\Enterprise\Common\Traits;

trait CommonLogging
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * Override the bootstrap method to boot me
     */
    public function bootstrap()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $_straps = array_flip( $this->bootstrappers );

        $_oldClass = $this->app->make( 'config' )->get( 'dfe.common.old-log-config-class' );
        $_newClass = $this->app->make( 'config' )->get( 'dfe.common.new-log-config-class' );

        if ( array_key_exists( $_oldClass, $_straps ) )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->bootstrappers[$_straps[$_oldClass]] = $_newClass;
        }

        /** @noinspection PhpUndefinedClassInspection */
        parent::bootstrap();
    }
}