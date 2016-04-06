<?php namespace DreamFactory\Enterprise\Common\Utility;

use Illuminate\Support\Collection;

/**
 * An ini file manipulator
 */
class Ini extends Collection
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The ini file
     */
    protected $file;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Creates and returns an instance of Ini
     *
     * @param array|mixed $file
     *
     * @return static
     */
    public static function makeFromFile($file)
    {
        $_ini = new static();
        $_ini->setFile($file);
        $_ini->load();

        return $_ini;
    }

    /**
     * Load the ini file
     *
     * @return bool
     */
    public function load()
    {
        if (!$this->file) {
            return false;
        }

        if (!file_exists($this->file) || !is_readable($this->file)) {
            throw new \InvalidArgumentException('The file "' . $this->file . '" is invalid or unreadable.');
        }

        $_lines = file($this->file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        foreach ($_lines as $_line) {
            $_parts = explode('=', $_line);
            if (2 == count($_parts)) {
                $_key = trim($_parts[0]);
                $_fc = $_key[0];
                $_value = trim($_parts[1]);

                //  Skip comments
                if (!in_array($_fc, ['#', ';'])) {
                    $this->put($_key, $_value);
                }
            }
        }

        return true;
    }

    /**
     * Save the ini file
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->file) {
            return false;
        }

        if (file_exists($this->file) && !is_writable($this->file)) {
            throw new \InvalidArgumentException('The file "' . $this->file . '" is invalid or unwritable.');
        }

        $_contents = null;

        foreach ($this->all() as $_key => $_value) {
            $_contents .= implode('=', [$_key, $_value]) . PHP_EOL;
        }

        if (false === file_put_contents($this->file, $_contents)) {
            throw new \RuntimeException('Error writing output to file "' . $this->file . '".');
        }

        return true;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     *
     * @return Ini
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @param string $string    The string to parse
     * @param string $delimiter The delimiter used. Defaults to "|" (pipe)
     *
     * @return array
     */
    public static function parseDelimitedString($string, $delimiter = '|')
    {
        if (empty($string)) {
            return [];
        }

        //  Ignore arrays...
        if (is_array($string)) {
            return $string;
        }

        //  No delimiter? Convert to array
        if (false === strpos($string, $delimiter)) {
            return empty($string) ? [] : [$string];
        }

        //  Parse the string
        return explode($delimiter, $string);
    }
}

