<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * A trait for executing system functions
 */
trait Executioner
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @var string
     */
    private $executionPath;
    /**
     * @type string The current working directory
     */
    private $currentWorkingPath;
    /**
     * @type string The prior working directory
     */
    private $priorWorkingPath;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string      $path
     * @param string      $command
     * @param string|null $output
     * @param string|null $returnValue
     *
     * @return null|string
     */
    public function execInPath($path, $command, &$output = null, &$returnValue = null)
    {
        return
            $this
                ->setExecutionPath($path)
                ->exec($command, $output, $returnValue);
    }

    /**
     * @param string      $command
     * @param string|null $output
     * @param string|null $returnValue
     *
     * @return null|string
     */
    public function exec($command, &$output = null, &$returnValue = null)
    {
        if ($this->pushCurrentPath()) {
            $_result = exec($command, $_output, $_return);
            $this->popCurrentPath();

            return $_result;
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function popCurrentPath()
    {
        if ($this->priorWorkingPath && chdir($this->priorWorkingPath)) {
            $this->priorWorkingPath = null;

            return true;
        }

        return false;
    }

    /**
     * @return $this
     */
    protected function pushCurrentPath()
    {
        if ($this->executionPath) {
            $this->priorWorkingPath = getcwd();

            if (chdir($this->executionPath)) {
                return true;
            }

            $this->priorWorkingPath = null;
        }

        return false;
    }

    /**
     * @param string $executionPath
     *
     * @return Executioner
     */
    public function setExecutionPath($executionPath)
    {
        $this->executionPath = realpath($executionPath);

        return $this;
    }
}
