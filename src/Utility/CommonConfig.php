<?php namespace DreamFactory\Enterprise\Common\Utility;

/**
 * Utilities to retrieve and cache common config values
 *
 * @package DreamFactory\Enterprise\Common\Utility
 */
class CommonConfig
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Retrieve and cache common config options
     */
    public static function initialize()
    {
        static::getCommonThemes();
        static::getCommonVersion();
    }

    /**
     * @param string $key    The config key to use
     * @param string $envKey The $_ENV key to use
     *
     * @return mixed
     */
    public static function getCommonVersion($key = 'dfe.common.display-version', $envKey = 'APP_VERSION')
    {
        //  Make the version info extra cool
        $_version = \Cache::get($key . '-cache', config($key) . '-' . substr(`git rev-parse --verify HEAD`, 0, 8));

        return static::bagAndTag($key, $_version, $envKey);
    }

    /**
     * @param string $key The config key to use
     * @param string $locationKey
     *
     * @return mixed
     */
    public static function getCommonThemes($key = 'dashboard.default-themes', $locationKey = 'dashboard.theme-locations')
    {
        if (empty($_themes = \Cache::get($key . '-cache'))) {
            $_themes = config($key, []);

            foreach (config($locationKey, []) as $_path) {
                /** @type \SplFileInfo $_theme */
                foreach (\File::allFiles(public_path($_path)) as $_theme) {
                    $_name = $_theme->getFilename();

                    if (is_file($_theme->getPathname()) && !in_array($_name = str_ireplace(['.min.css', '.css'], null, $_name), $_themes)) {
                        $_themes[] = $_name;
                    }
                }
            }

            //  Clean up
            foreach ($_themes as $_key => $_theme) {
                $_themes[$_key] = trim(studly_case($_theme));
            }

            sort($_themes);
        }

        return static::bagAndTag($key, $_themes);
    }

    /**
     *  Stuffs a value into the cache and environment
     *
     * @param string $key   The config key
     * @param string $env   The env key
     * @param null   $value The value
     * @param int    $ttl
     *
     * @return mixed
     */
    public static function bagAndTag($key, $value = null, $env = null, $ttl = 15)
    {
        \Cache::put($key . '-cache', $value, $ttl);

        is_array($key) ? config($key) : config([$key => $value]);
        !empty($env) && putenv($env . '=' . $value) && $_ENV[$env] = $value;

        return $value;
    }
}
