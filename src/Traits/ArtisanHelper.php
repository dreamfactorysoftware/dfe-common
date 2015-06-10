<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Library\Utility\JsonFile;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A trait that adds shortcuts for artisan commands
 */
trait ArtisanHelper
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string An optional prefix, such as the command name, which will be prepended to output
     */
    private $outputPrefix = false;
    /**
     * @type string The currently buffered output
     */
    private $lineBuffer = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Displays the command's name and info
     *
     * @param bool $newline If true, a blank line is added to the end of the header
     *
     * @return $this
     */
    protected function writeHeader( $newline = true )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output->writeln(
            $this->context( config( 'dfe.commands.display-name' ), 'info' ) .
            ' (' . $this->context( config( 'dfe.commands.display-version', 'Alpha' ), 'comment' ) . ')'
        );

        if ( null !== ( $_copyright = config( 'dfe.commands.display-copyright' ) ) )
        {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->output->writeln( $this->context( $_copyright, 'info' ) . ( $newline ? PHP_EOL : null ) );
        }

        return $this;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function writeln( $messages, $context = null, $type = OutputInterface::OUTPUT_NORMAL )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output->writeln( $this->formatMessages( $messages, $context ), $type );
    }

    /**
     * @param string|array $messages
     * @param bool         $newline
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function write( $messages, $newline = false, $context = null, $type = OutputInterface::OUTPUT_NORMAL )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output->write( $this->formatMessages( $messages, $context ), $newline, $type );
    }

    /**
     * @param string $content The content to wrap
     * @param string $tag     The tag to wrap content
     *
     * @return string
     */
    protected function context( $content, $tag )
    {
        return '<' . $tag . '>' . $content . '</' . $tag . '>';
    }

    /**
     * Buffers a string (optionally contextual) to write when flush() is called
     *
     * @param string $text
     *
     * @return $this
     */
    protected function concat( $text )
    {
        //  Initialize to an array and add text
        false === $this->lineBuffer && ( $this->lineBuffer = [] );
        $this->lineBuffer[] = $text;

        return $this;
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string|array $messages
     *
     * @return $this
     */
    protected function asInfo( $messages )
    {
        return $this->concat( $this->context( $messages, 'info' ) );
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string|array $messages
     *
     * @return $this
     */
    protected function asComment( $messages )
    {
        return $this->concat( $this->context( $messages, 'comment' ) );
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string|array $messages
     *
     * @return $this
     */
    protected function asQuestion( $messages )
    {
        return $this->concat( $this->context( $messages, 'question' ) );
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string|array $messages
     *
     * @return $this
     */
    protected function asError( $messages )
    {
        return $this->concat( $this->context( $messages, 'error' ) );
    }

    /**
     * Writes any buffered text and clears the buffer
     *
     * @param string|null $message Any text to add to the buffer before flushing
     * @param string|null $context The context of $message
     */
    protected function flush( $message = null, $context = null )
    {
        ( $message && $context ) && $this->concat( $this->context( $message, $context ) );

        !empty( $this->lineBuffer ) && $this->writeln( $this->formatMessages( implode( '', $this->lineBuffer ), null, false ) );
        $this->lineBuffer = false;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param bool         $prefix  If false, text will not be prefixed
     *
     * @return array|string
     */
    protected function formatMessages( $messages, $context = null, $prefix = true )
    {
        $_scrubbed = [];
        $_data = !is_array( $messages ) ? [$messages] : $messages;

        if ( !empty( $this->outputPrefix ) && ': ' != substr( $this->outputPrefix, -2 ) )
        {
            $this->outputPrefix = trim( $this->outputPrefix, ':' ) . ': ';
        }

        foreach ( $_data as $_message )
        {
            $context && ( $_message = $this->context( trim( $_message ), $context ) );
            $_scrubbed[] = ( $prefix && $this->outputPrefix ? $this->outputPrefix : null ) . $_message;
        }

        return is_array( $messages ) ? $_scrubbed : $_scrubbed[0];
    }

    /**
     * Retrieve any configuration settings for a command.
     *
     * @param string|null $command The command in question. If not specified, derived from $this->name minus 'dfe:' prefix
     *
     * @return array
     */
    protected function getCommandConfig( $command = null )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return config( 'dfe.commands.' . $command ?: str_replace( 'dfe:', null, $this->name ), [] );
    }

    /**
     * @param string|null $optionKey
     * @param string|null $arrayKey
     * @param array       $array
     * @param bool        $required
     *
     * @return bool
     */
    protected function optionString( $optionKey = null, $arrayKey = null, array &$array = null, $required = false )
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $_string = $this->option( $optionKey );

        if ( $required && empty( $_string ) )
        {
            $this->writeln( '"' . $optionKey . '" is a required option for this operation.' );

            return false;
        }

        !empty( $_string ) && ( $array[$arrayKey] = $_string );

        return true;
    }

    /**
     * Retrieves an input argument and checks for valid JSON.
     *
     * @param string|null $optionKey The option name to retrieve
     * @param string|null $arrayKey  If specified, decoded array will be placed into $array[$arrayKey]
     * @param array|null  $array     The $array in which to place the result
     * @param bool        $required  If this is required
     *
     * @return bool|array
     */
    protected function optionArray( $optionKey = null, $arrayKey = null, array &$array = null, $required = false )
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $_data = $this->option( $optionKey );

        if ( null === $arrayKey )
        {
            return $_data;
        }

        if ( empty( $_data ) )
        {
            if ( $required )
            {
                $this->writeln( '"' . $optionKey . '" is a required option for this operation.' );

                return false;
            }

            $array[$arrayKey] = $_data = [];

            return true;
        }

        try
        {
            $_data = JsonFile::decode( $_data );
        }
        catch ( \Exception $_ex )
        {
            $this->writeln( 'the "' . $optionKey . '" provided does not contain valid JSON.' );

            return false;
        }

        $array[$arrayKey] = $_data;

        return true;
    }

    /**
     * @return string
     */
    public function getOutputPrefix()
    {
        return $this->outputPrefix;
    }

    /**
     * @param string $outputPrefix
     *
     * @return ArtisanHelper
     */
    public function setOutputPrefix( $outputPrefix )
    {
        $this->outputPrefix = $outputPrefix;

        return $this;
    }

}
