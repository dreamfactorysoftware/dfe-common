<?php namespace DreamFactory\Enterprise\Common\Traits;

/**
 * Provides a means to store log files in a place other than /storage/logs/laravel.log
 *
 * @package DreamFactory\Enterprise\Common\Traits
 */
trait CommonLogging
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * Override the bootstrap method to boot me
     */
    public function bootstrap()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $_straps = array_flip($this->bootstrappers);

        /** @noinspection PhpUndefinedFieldInspection */
        $_oldClass = config('dfe.common.logging.old-log-config-class');
        /** @noinspection PhpUndefinedFieldInspection */
        $_newClass = config('dfe.common.logging.new-log-config-class');

        if (array_key_exists($_oldClass, $_straps)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->bootstrappers[$_straps[$_oldClass]] = $_newClass;
        }

        /** @noinspection PhpUndefinedClassInspection */
        /** @noinspection PhpUndefinedMethodInspection */
        parent::bootstrap();
    }
}