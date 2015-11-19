<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Contracts\VersionController;
use DreamFactory\Enterprise\Common\Traits\Executioner;
use DreamFactory\Library\Utility\Disk;
use Illuminate\Contracts\Foundation\Application;

class GitService extends BaseService implements VersionController
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Executioner;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The repository path
     */
    protected $repositoryPath;
    /**
     * @type string The base repository path
     */
    protected $repositoryBasePath;
    /**
     * @type string The name of the ".git" path
     */
    protected $gitPath;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Application $app
     * @param string      $repositoryBasePath
     * @param string|null $repositoryPath The sub-directory for this instance
     */
    public function __construct($app, $repositoryBasePath, $repositoryPath = null)
    {
        parent::__construct($app);

        $this->gitPath = env('GIT_PATH', '.git');

        $this
            ->setRepositoryBasePath($repositoryBasePath)
            ->setRepositoryPath($repositoryPath);
    }

    /** @inheritdoc */
    public function init(array $arguments = [], &$output = null, &$returnValue = null)
    {
        //  Save the current exec path
        $_path = $this->executionPath;

        //  Set it to our base...
        $this->setExecutionPath($this->repositoryBasePath);

        if ($this->pushCurrentPath()) {
            $_result = $this->exec('git init ' . $this->makeArguments($arguments) . ' ./ 2>&1', $output, $returnValue);

            $this->popCurrentPath();
        }

        //  Restore
        $this->setExecutionPath($_path);

        return 0 == $returnValue;
    }

    /** @inheritdoc */
    public function checkout($revision, array $arguments = [], &$output = null, &$returnValue = null)
    {
        return $this->exec('git checkout ' . $revision . ' ' . $this->makeArguments($arguments) . ' 2>&1',
            $output,
            $returnValue);
    }

    /** @inheritdoc */
    public function commitAllChanges($message, &$output = null, &$returnValue = null)
    {
        $this->exec('git add --all 2>&1', $_output, $_return);
        0 == $_return && $this->exec('git commit -a -m ' . escapeshellarg($message) . ' 2>&1', $output, $returnValue);

        return $_return;
    }

    /** @inheritdoc */
    public function commitChange($file, $message, &$output = null, &$returnValue = null)
    {
        $this->exec('git add ' . escapeshellarg($file) . ' 2>&1', $output, $returnValue);
        0 == $returnValue && $this->exec('git commit -m ' . escapeshellarg($message) . ' 2>&1', $output, $returnValue);

        return $returnValue;
    }

    /**
     * @return string
     */
    public function getCurrentBranch()
    {
        $this->exec('git rev-parse --abbrev -ref HEAD 2>&1', $_output, $_return);

        return trim(explode(' ', $_output));
    }

    /** @inheritdoc */
    public function getRevisions(&$output = null, &$returnValue = null)
    {
        $_revisions = [];

        $this->exec('git log --pretty="%H %at %s" --no-merges 2>&1', $_output, $_return);

        if (!empty($_output)) {
            foreach ($_output as $_entry) {
                list($_revision, $_timestamp, $_subject) = explode(' ', $_entry);

                $_revisions[] = [
                    'date'     => date('c', $_timestamp),
                    'subject'  => $_subject,
                    'revision' => $_revision,
                ];
            }
        }

        return $_revisions;
    }

    /** @inheritdoc */
    public function setRepositoryBasePath($repositoryBasePath)
    {
        $repositoryBasePath && $this->repositoryBasePath = $repositoryBasePath;

        return $this;
    }

    /** @inheritdoc */
    public function setRepositoryPath($repositoryPath)
    {
        if (!empty($repositoryPath)) {
            $this->setExecutionPath(realpath(Disk::path([
                $this->repositoryBasePath,
                $this->repositoryPath = $repositoryPath,
            ],
                true)));
        }

        return $this;
    }

    /**
     * Converts an array of key value pairs into command line arguments
     *
     * @param array $arguments
     *
     * @return null|string
     */
    protected function makeArguments(array $arguments = [])
    {
        $_args = null;

        foreach ($arguments as $_key => $_value) {
            $_key = ('--' != substr($_key, 0, 2)) ? '-' . $_key . ' ' : $_key . '=';
            $_args = $_key . escapeshellarg($_value);
        }

        return $_args;
    }
}
