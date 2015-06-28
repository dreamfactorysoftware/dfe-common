<?php namespace DreamFactory\Enterprise\Common\Traits;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * A trait that aids with archiving
 */
trait Archivist
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Stream writer with graceful fallback
     *
     * @param Filesystem $filesystem
     * @param string     $source
     * @param string     $destination
     *
     * @return bool
     */
    protected function writeStream($filesystem, $source, $destination)
    {
        if (false !== ($_fd = fopen($source, 'r'))) {
            //  Fallback gracefully if no stream support
            if (method_exists($filesystem, 'writeStream')) {
                $_result = $filesystem->writeStream($destination, $_fd, []);
            } elseif (method_exists($filesystem->getAdapter(), 'writeStream')) {
                $_result = $filesystem->getAdapter()->writeStream($destination, $_fd, $filesystem->getConfig());
            } else {
                $_result = $filesystem->put($destination, file_get_contents($source));
            }

            fclose($_fd);

            return $_result;
        }

        return false;
    }

    /**
     * @param Filesystem $source      The source file system to archive
     * @param string     $archiveFile The name of the archive/zip file. Extension is optional, allowing me to decide
     *
     * @return bool|string If successful, the actual file name (without a path) is return. False otherwise
     */
    protected function archiveTree(Filesystem $source, $archiveFile)
    {
        //  Add file extension if missing
        $archiveFile = $this->ensureFileSuffix('.zip', $archiveFile);

        //  Create our zip container
        $_archive = new Filesystem(new ZipArchiveAdapter($archiveFile));

        try {
            foreach ($source->listContents('', true) as $_file) {
                if ('dir' == $_file['type']) {
                    $_archive->createDir($_file['path']);
                } elseif ('link' == $_file['type']) {
                    $_archive->put($_file['path'], $_file['target']);
                } elseif ('file' == $_file['type']) {
                    file_exists($_file['path']) && $this->writeStream($_archive, $_file['path'], $_file['path']);
                }
            }
        } catch (\Exception $_ex) {
            \Log::error('Exception exporting instance storage: ' . $_ex->getMessage());

            return false;
        }

        //  Force-close the zip
        /** @noinspection PhpUndefinedMethodInspection */
        $_archive->getAdapter()->getArchive()->close();
        $_archive = null;

        return basename($archiveFile);
    }

    /**
     * Moves a file from the working directory to the destination archive, optionally deleting afterwards.
     *
     * @param Filesystem|\Illuminate\Contracts\Filesystem\Filesystem $archive
     * @param string                                                 $workFile
     * @param bool                                                   $delete If true, file is deleted from work space after being moved
     */
    protected function moveWorkFile($archive, $workFile, $delete = true)
    {
        if ($this->writeStream($archive, $workFile, basename($workFile))) {
            $delete && unlink($workFile);
        }
    }

    /**
     * @param string $tag      Unique identifier for temp space
     * @param bool   $pathOnly If true, only the path is returned.
     *
     * @return \League\Flysystem\Filesystem|string
     */
    protected function getWorkPath($tag, $pathOnly = false)
    {
        $_root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dfe' . DIRECTORY_SEPARATOR . $tag;

        if (!\DreamFactory\Library\Utility\FileSystem::ensurePath($_root)) {
            throw new \RuntimeException('Unable to create working directory "' . $_root . '". Aborting.');
        }

        if ($pathOnly) {
            return $_root;
        }

        //  Set our temp base
        return new Filesystem(new Local($_root));
    }

    /**
     * Deletes a previously made work path
     *
     * @param string $tag Unique identifier for temp space
     *
     * @return bool
     */
    protected function deleteWorkPath($tag)
    {
        $_root = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'dfe' . DIRECTORY_SEPARATOR . $tag;

        if (is_dir($_root)) {
            return \DreamFactory\Library\Utility\FileSystem::rmdir($_root);
        }

        return true;
    }

    /**
     * Ensures a file name has the proper suffix
     *
     * @param string $suffix
     * @param string $file
     *
     * @return string
     */
    protected function ensureFileSuffix($suffix, $file)
    {
        if ($suffix !== strtolower(substr($file, -(strlen($suffix))))) {
            $file .= $suffix;
        }

        return $file;
    }

    /**
     * Force-closes a zip archive, writing to disk
     *
     * @param \League\Flysystem\Filesystem $filesystem
     */
    protected function flushZipArchive(Filesystem $filesystem)
    {
        if (($_adapter = $filesystem->getAdapter()) instanceof ZipArchiveAdapter) {
            $_adapter->getArchive()->close();
        }
    }
}
