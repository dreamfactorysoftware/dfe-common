<?php
/**
 * Configuration file for the dfe-common library
 */

return [
    /** Global options */
    'display-name'      => 'DreamFactory Enterprise',
    'display-version'   => 'v1.0.x-alpha',
    'display-copyright' => 'Copyright (c) 2012-' . date( 'Y' ) . ', All Rights Reserved.',
    /**
     * Theme selection -- a bootswatch theme name
     * Included are cerulean, darkly, flatly, paper, and superhero.
     * You may also install other compatible themes and use them as well.
     */
    'themes'            => ['auth' => 'darkly', 'page' => 'flatly'],
    'log-path'          => env( 'DFE_LOG_PATH' ),
    'log-file-name'     => env( 'DFE_LOG_FILE_NAME' ),
];