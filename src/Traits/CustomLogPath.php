<?php namespace DreamFactory\Enterprise\Common\Traits;

trait CustomLogPath
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $_clpOldClass;
    /**
     * @type string
     */
    protected $_clpNewClass;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * Override the bootstrap method to boot me
     */
    public function bootstrap()
    {
        $this->boot();

        /** @noinspection PhpUndefinedMethodInspection */
        parent::bootstrap();
    }

    /**
     * Boot up
     */
    public function boot()
    {
        $this->_clpOldClass = config( 'dfe.common.old-log-config-class' );
        $this->_clpNewClass = config( 'dfe.common.new-log-config-class' );

        $this->_replaceLoggingConfigurationClass();
    }

    /**
     * Make the replacements if all is good...
     */
    private function _replaceLoggingConfigurationClass()
    {
        if ( !empty( $this->_clpNewClass ) && !empty( $this->_clpOldClass ) )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $_straps = array_flip( $this->bootstrappers );

            if ( array_key_exists( $this->_clpOldClass, $_straps ) )
            {
                /** @noinspection PhpUndefinedFieldInspection */
                $this->bootstrappers[$_straps[$this->_clpOldClass]] = $this->_clpNewClass;
            }
        }
    }
}