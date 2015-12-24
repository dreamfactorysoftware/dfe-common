<?php namespace DreamFactory\Enterprise\Common\Commands;

use DreamFactory\Enterprise\Common\Traits\ArtisanHelper;
use DreamFactory\Enterprise\Common\Traits\ArtisanOptionHelper;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Composer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Adds some additional functionality to the Command class
 */
abstract class ConsoleCommand extends Command
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use DispatchesJobs, ArtisanHelper, ArtisanOptionHelper;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type \Illuminate\Foundation\Composer The Composer class instance.
     */
    protected $composer;
    /**
     * @type \Illuminate\Filesystem\Filesystem The filesystem instance.
     */
    protected $filesystem;
    /**
     * @type bool Overridden --quiet indicator
     */
    protected $wasQuiet = false;
    /**
     * @type string The output format
     */
    protected $format;

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

        $this->composer = $composer;
        $this->filesystem = $filesystem;
    }

    /**
     * The guts of the command
     *
     * @todo make abstract after fire() deprecation
     */
    public function handle()
    {
        //  Does nothing. Will be abstract in a future version
    }

    /**
     * Hijack execute to turn off quiet
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //  No being quiet allowed...
        if (true === ($this->wasQuiet = (OutputInterface::VERBOSITY_QUIET === $output->getVerbosity()))) {
            $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
        }

        //  Get the output format, if any
        if ($input->hasOption('format')) {
            if (empty($this->format = strtolower(trim($this->option('format'))))) {
                $this->format = null;
            }
        }

        //  No header when quiet or formatted data output...
        if (null === $this->format && !$this->wasQuiet) {
            $this->writeHeader();
        }

        //  Do the execute
        $_result = parent::execute($input, $output);

        //  Restore verbosity and return
        $this->wasQuiet && $output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        return $_result;
    }

    /**
     * Handle the command
     *
     * @deprecated use static::handle() instead
     */
    public function fire()
    {
        return $this->handle();
    }
}
