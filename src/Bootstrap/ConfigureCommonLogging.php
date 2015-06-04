<?php namespace DreamFactory\Enterprise\Common\Bootstrap;

use DreamFactory\Library\Utility\FileSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Bootstrap\ConfigureLogging;
use Illuminate\Log\Writer;

class ConfigureCommonLogging extends ConfigureLogging
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
    protected $_useCommonLogging = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Application $app
     */
    public function bootstrap( Application $app )
    {
        $this->_logFileName = $app->make( 'config' )->get( 'dfe.common.logging.log-file-name' );

        if ( null !== ( $this->_logPath = $app->make( 'config' )->get( 'dfe.common.logging.log-path' ) ) )
        {
            $this->_logPath = rtrim( $this->_logPath, ' ' . DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        }

        $this->_useCommonLogging =
            ( !empty( $this->_logPath ) && !empty( $this->_logFileName ) )
                ? FileSystem::ensurePath( $this->_logPath )
                : false;

        parent::bootstrap( $app );
    }

    /** @inheritdoc */
    protected function configureSingleHandler( Application $app, Writer $log )
    {
        if ( !$this->useCommonLogging() )
        {
            parent::configureSingleHandler( $app, $log );

            return;
        }

        $_file = $this->_logPath . $this->_logFileName;
        $log->useFiles( $_file );
    }

    /** @inheritdoc */
    protected function configureDailyHandler( Application $app, Writer $log )
    {
        if ( !$this->useCommonLogging() )
        {
            parent::configureDailyHandler( $app, $log );

            return;
        }

        $_file = $this->_logPath . $this->_logFileName;
        $log->useDailyFiles( $_file, $app->make( 'config' )->get( 'app.log_max_files', 5 ) );
    }

    /**
     * @return boolean
     */
    public function useCommonLogging()
    {
        return $this->_useCommonLogging;
    }

    /**
     * @return string
     */
    public function getCommonLogPath()
    {
        return $this->_logPath;
    }

    /**
     * @return string
     */
    public function getCommonLogFileName()
    {
        return $this->_logFileName;
    }
}