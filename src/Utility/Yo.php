<?php namespace DreamFactory\Enterprise\Common\Utility;

/**
 * Yo! Session "flash" message helpers
 */
class Yo
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * A one-time use "alert", or Public Service Announcement (PSA)
     *
     * @param string      $title   The title within the div/container
     * @param null        $content The content within the div/container
     * @param string      $context The context of the div/container
     * @param string|null $id      Optional "id" attribute for the created alert div/container
     * @param bool|false  $hidden  If true, the container is hidden. Ignored if $id === null
     *
     * @return string
     */
    public static function psa($title, $content = null, $context = 'alert-info', $id = null, $hidden = false)
    {
        $id && $hidden && $context .= ' hide';

        return view('app.templates.psa', [
            'alert_id' => $id,
            'context'  => $context,
            'title'    => $title,
            'content'  => $content,
        ])->render();
    }

    /**
     * @todo refactor to use static::psa
     *
     * Checks for a success/failure flash message and renders the "alert" HTML.
     * Uses values from /resources/lang/en/dashboard.php
     *
     * @param string|null $prefix      A key prefix, if any. A dot (".") will be auto-appended
     * @param bool        $errorsFixed If true, the "alert-fixed" class is added to the alert container
     *
     * @return null|string The rendered HTML or null
     */
    public static function alert($prefix = null, $errorsFixed = true)
    {
        //  Clean up the prefix
        $prefix = empty($prefix) ? null : rtrim($prefix, '. ');

        $_true = $prefix . 'success';
        $_false = $prefix . 'failure';

        if (\Session::has($_false)) {
            $_data = [
                'flash'   => \Session::get($_false),
                'title'   => \Lang::get('failure.title'),
                'context' => 'alert-danger' . ($errorsFixed ? ' alert-fixed' : null),
            ];
        } elseif (\Session::has($_true)) {
            $_data = [
                'flash'   => \Session::get($_true),
                'title'   => \Lang::get('success.title'),
                'context' => 'alert-success',
            ];
        } else {
            return null;
        }

        return view('app.templates.flash-alert', $_data)->render();
    }
}