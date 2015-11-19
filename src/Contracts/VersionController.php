<?php namespace DreamFactory\Enterprise\Common\Contracts;

/**
 * Describes an object that offers version control
 */
interface VersionController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Initialize/create a repository in $repositoryBasePath
     *
     * @param array       $arguments   Any arguments to pass to vcs init in the form of ['o'=>'short-option-value','--long-option' => 'long-option-value']
     * @param string|null $output      The output of command
     * @param string|null $returnValue The shell return value of command
     *
     * @return bool
     */
    public function init(array $arguments = [], &$output = null, &$returnValue = null);

    /**
     * Set the repository base path
     *
     * @param string $repositoryBasePath
     *
     * @return VersionController
     */
    public function setRepositoryBasePath($repositoryBasePath);

    /**
     * @param string $repositoryPath The sub-repo path relative to $repositoryBasePath
     *
     * @return VersionController
     */
    public function setRepositoryPath($repositoryPath);

    /**
     * @param string      $revision
     * @param array       $arguments
     * @param string|null $output      The output of command
     * @param string|null $returnValue The shell return value of command
     *
     * @return null|string
     */
    public function checkout($revision, array $arguments = [], &$output = null, &$returnValue = null);

    /**
     * @param string      $file        The relative file to commit
     * @param string      $message     The commit message
     * @param string|null $output      The output of command
     * @param string|null $returnValue The shell return value of command
     *
     * @return int
     */
    public function commitChange($file, $message, &$output = null, &$returnValue = null);

    /**
     * @param string      $message     The commit message
     * @param string|null $output      The output of command
     * @param string|null $returnValue The shell return value of command
     *
     * @return int
     */
    public function commitAllChanges($message, &$output = null, &$returnValue = null);

    /**
     * @param string|null $output      The output of command
     * @param string|null $returnValue The shell return value of command
     *
     * @return array
     */
    public function getRevisions(&$output = null, &$returnValue = null);
}
