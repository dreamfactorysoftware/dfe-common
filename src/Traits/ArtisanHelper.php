<?php namespace DreamFactory\Enterprise\Common\Traits;

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
     * @type OutputInterface
     */
    protected $_ahOutputInterface;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutputInterface( OutputInterface $output )
    {
        $this->_ahOutputInterface = $output;

        return $this;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function _writeln( $messages, $context = null, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_ahOutputInterface->writeln( $this->_ahScrubMessages( $messages, $context ), $type );
    }

    /**
     * @param string|array $messages
     * @param bool         $newline
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function _write( $messages, $newline = false, $context = null, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_ahOutputInterface->write( $this->_ahScrubMessages( $messages, $context ), $newline, $type );
    }

    /**
     * @param string $tag       The tag to wrap content
     * @param null   $content   The content to wrap
     * @param bool   $leaveOpen If true, no closing tag will be added. Otherwise, closing tag is always added.
     *
     * @return string
     */
    protected function _ahWrap( $tag, $content = null, $leaveOpen = false )
    {
        if ( empty( $tag ) )
        {
            return $content;
        }

        return '<' . $tag . '>' . $content . ( $leaveOpen ? null : '</' . $tag . '>' );
    }

    /**
     * @param string|array $messages
     * @param int          $type
     *
     * @return $this
     */
    protected function _ahEcho( $messages, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_writeln( $messages, null, $type );

        return $this;
    }

    /**
     * @param string|array $messages
     * @param int          $type
     *
     * @return $this
     */
    protected function _ahInfo( $messages, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_writeln( $messages, 'info', $type );

        return $this;
    }

    /**
     * @param string|array $messages
     * @param int          $type
     *
     * @return $this
     */
    protected function _ahError( $messages, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_writeln( $messages, 'error', $type );

        return $this;
    }

    /**
     * @param string|array $messages
     * @param int          $type
     *
     * @return $this
     */
    protected function _ahQuestion( $messages, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_writeln( $messages, 'question', $type );

        return $this;
    }

    /**
     * @param string|array $messages
     * @param int          $type
     *
     * @return $this
     */
    protected function _ahComment( $messages, $type = OutputInterface::OUTPUT_NORMAL )
    {
        $this->_writeln( $messages, 'comment', $type );

        return $this;
    }

    /**
     * @param string       $prefix        Program name, statement, etc.
     * @param string|array $messages      The message(s)
     * @param string       $context       An output context "info", "comment", "error", or "question" for the messages
     * @param null         $prefixContext An output context "info", "comment", "error", or "question" for the prefix only.
     * @param bool         $addColon      If true, a colon will be appended to the prefix before concatenation
     *
     * @return array|string
     */
    protected function _ahPrefixOutput( $prefix, $messages, $context = null, $prefixContext = null, $addColon = true )
    {
        $_prefixed = [];
        $_data = is_array( $messages ) ? $messages : [$messages];
        $_prefix = trim( $this->_ahWrap( $prefixContext, $prefix ) . ( $addColon ? ':' : null ) );

        foreach ( $_data as $_message )
        {
            $context && ( $_message = $this->_ahWrap( $context, trim( $_message ) ) );
            $_prefixed[] = $_prefix . ' ' . trim( $_message );
        }

        return is_array( $messages ) ? $_prefixed : $_prefixed[0];
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     *
     * @return array|string
     */
    protected function _ahScrubMessages( $messages, $context = null )
    {
        $_scrubbed = [];
        $_data = !is_array( $messages ) ? [$messages] : $messages;

        foreach ( $_data as $_message )
        {
            $context && ( $_message = $this->_ahWrap( $context, trim( $_message ) ) );
            $_scrubbed[] = $_message;
        }

        return is_array( $messages ) ? $_scrubbed : $_scrubbed[0];
    }

    /**
     * Displays the command's name and info
     *
     * @param string $command The command name
     * @param bool   $newline If true, a blank line is added to the end of the header
     */
    protected function _ahShowHeader( $command, $newline = true )
    {
        $this->_writeln(
            $this->_ahWrap( 'info', config( 'dfe.commands.' . $command . '.display-name' ) ) .
            ' (' . $this->_ahWrap( 'comment', config( 'dfe.commands.' . $command . '.display-version', 'v0.0.0' ) ) . ')'
        );

        if ( null !== ( $_copyright = config( 'dfe.commands.' . $command . '.display-copyright' ) ) )
        {
            $this->_writeln( $this->_ahWrap( 'info', $_copyright ) . ( $newline ? PHP_EOL : null ) );
        }
    }
}
