<?php namespace DreamFactory\Enterprise\Common\Bootstrap;

use DreamFactory\Library\Utility\FileSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging as BaseLoggingConfiguration;
use Illuminate\Log\Writer;

class CommonLoggingConfiguration extends BaseLoggingConfiguration
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $_logPath;
    /**
     * @type string
     */
    protected $_logFileName;
    /**
     * @type bool
     */
    protected $_useCustomPlacement = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function bootstrap( Application $app )
    {
        $this->_logFileName = config( 'dfe.common.log-file-name' );

        if ( null !== ( $this->_logPath = config( 'dfe.common.log-path' ) ) )
        {
            $this->_logPath = rtrim( $this->_logPath, ' ' . DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        }

        $this->_useCustomPlacement =
            ( !empty( $this->_logPath ) && !empty( $this->_logFileName ) )
                ? FileSystem::ensurePath( $this->_logPath )
                : false;

        parent::bootstrap( $app );
    }

    /** @inheritdoc */
    protected function configureSingleHandler( Application $app, Writer $log )
    {
        if ( $this->_useCustomPlacement )
        {
            $_file = $this->_logPath . $this->_logFileName;
            $log->useFiles( $_file );

            return;
        }

        parent::configureSingleHandler( $app, $log );
    }

    /** @inheritdoc */
    protected function configureDailyHandler( Application $app, Writer $log )
    {
        if ( $this->_useCustomPlacement )
        {
            $_file = $this->_logPath . $this->_logFileName;

            $log->useDailyFiles( $_file, $app->make( 'config' )->get( 'app.log_max_files', 5 ) );

            return;
        }

        parent::configureDailyHandler( $app, $log );
    }

}