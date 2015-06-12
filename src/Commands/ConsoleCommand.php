<?php namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\ArtisanHelper;
use DreamFactory\Enterprise\Common\Traits\ArtisanOptionHelper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Composer;

/**
 * Adds some additional functionality to the Command class
 */
abstract class ConsoleCommand extends Command
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use ArtisanHelper, ArtisanOptionHelper;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type \Illuminate\Foundation\Composer The Composer class instance.
     */
    protected $_composer;
    /**
     * @type \Illuminate\Filesystem\Filesystem The filesystem instance.
     */
    protected $_filesystem;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Composer   $composer
     * @param Filesystem $filesystem
     */
    public function __construct(Composer $composer, Filesystem $filesystem)
    {
        parent::__construct();

        $this->_composer = $composer;
        $this->_filesystem = $filesystem;
    }

    /**
     * Handle the command
     */
    public function fire()
    {
        if (null === $this->getOutputPrefix()) {
            $this->setOutputPrefix(str_replace('dfe:', null, $this->name));
        }

        $this->writeHeader();
    }
}