<?php namespace DreamFactory\Enterprise\Common\Utility;

use DreamFactory\Enterprise\Common\Enums\ManifestTypes;
use League\Flysystem\Filesystem;

/**
 * Writes config files
 */
class ConfigWriter
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string      $filename
     * @param array       $contents
     * @param string|null $comment
     *
     * @return bool
     */
    public static function make($filename, array $contents = [], $comment = null)
    {
        $filename = str_ireplace('.php', null, $filename) . '.php';
        $comment = $comment ?: '// This file was automatically generated on ' . date('c') . ' by dfe.common.config-writer';
        $_text = ['<?php', $comment, 'return ['];

        foreach ($contents as $_key => $_value) {
            if (is_scalar($_value)) {
                $_quote = is_numeric($_value)
                    ? null
                    : false === strpos($_value, '\'')
                        ? '\''
                        : '"';
                $_text[] =
                    '    ' . (is_numeric($_key) ? $_key : $_quote . $_key . $_quote) . ' => ' . $_quote . $_value . $_quote . ',';
            } else {
                $_text[] = var_export($_value, true) . ',';
            }
        }

        $_text[] = '];';

        return false !== file_put_contents(config_path($filename), implode(PHP_EOL, $_text));
    }
}
