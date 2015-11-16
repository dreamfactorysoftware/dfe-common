<?php namespace DreamFactory\Enterprise\Common\Services;


use DreamFactory\Enterprise\Common\Contracts\VersionController;
use DreamFactory\Enterprise\Common\Traits\Executioner;
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
    protected $path;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param Application|null $app
     * @param string|null      $path
     */
    public function __construct($app = null, $path = null)
    {
        parent::__construct($app);

        $this->setExecutionPath($path);
    }

    /**
     * @param string $revision
     *
     * @return null|string
     */
    public function checkout($revision)
    {
        return $this->exec('git checkout ' . $revision . ' 2>&1', $_output, $_return);
    }

    /**
     * @param string $message The commit message
     *
     * @return int
     */
    public function commitAllChanges($message)
    {
        $this->exec('git add --all 2>&1', $_output, $_return);
        0 == $_return && $this->exec('git commit -a -m ' . escapeshellarg($message) . ' 2>&1', $_output, $_return);

        return $_return;
    }

    /**
     * @param string $file    The relative file to commit
     * @param string $message The commit message
     *
     * @return int
     */
    public function commitChange($file, $message)
    {
        $this->exec('git add ' . escapeshellarg($file) . ' 2>&1', $_output, $_return);
        0 == $_return && $this->exec('git commit -m ' . escapeshellarg($message) . ' 2>&1', $_output, $_return);

        return $_return;
    }

    /**
     * @return string
     */
    public function getCurrentBranch()
    {
        $this->exec('git rev-parse --abbrev-ref HEAD', $_output, $_return);

        return trim(explode(' ', $_output));
    }

    /**
     * @return array
     */
    public function getRevisions()
    {
        $_revisions = [];

        $this->exec('git log --pretty="%H %at %s" --no-merges', $_output, $_return);

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

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setRepository($path)
    {
        $path && $this->path = realpath($path);

        return $this;
    }
}
