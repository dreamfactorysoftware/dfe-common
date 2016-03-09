<?php namespace DreamFactory\Enterprise\Common\Utility;

/**
 * UI/UX utils
 */
class UX
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Returns an array of data suitable for making a toolbar button
     *
     * @param string|int $id
     * @param string     $text
     * @param array      $options
     *
     * @return array
     */
    public static function makeInstanceToolbarButton($id, $text, array $options = [])
    {
        $_action = trim(strtolower(str_replace(['_', ' '], '-', $text)), ' -');

        $_template = [
            'id'         => 'instance-' . $_action . '-' . $id,
            'text'       => trim($text),
            'type'       => 'button',
            'size'       => 'btn-xs',
            'context'    => 'btn-info',
            'icon'       => null,
            'hint'       => null,
            'icon-class' => 'instance-toolbar-button',
            'data'       => [],
            'action'     => $_action,
        ];

        $_data = array_merge($_template, $options);

        !empty($_icon = array_get($_data, 'icon')) &&
        $_data['icon'] = '<i class="fa fa-fw ' . $_icon . ' ' . array_get($_data, 'icon-class', $_template['icon-class']) . '"></i>';
        !empty($_hint = array_get($_data, 'hint')) && $_data['hint'] = trim($text . ' instance');

        $_data['data'] = array_merge($_data['data'],
            [
                'instance-id'     => $id,
                'instance-action' => $_action,
            ]);

        return array_merge($_template, $_data);
    }

    /**
     * @param string $id        The instance id
     * @param bool   $startStop If true, start/stop/terminate buttons are added as well
     *
     * @return array The default instance toolbar buttons
     */
    public static function makeInstanceToolbarButtons($id, $startStop = false)
    {
        $_buttons = [
            'launch' => static::makeInstanceToolbarButton($id, 'Launch', ['context' => 'btn-success', 'icon' => 'fa-play']),
            'delete' => static::makeInstanceToolbarButton($id, 'Delete', ['context' => 'btn-danger', 'icon' => 'fa-times']),
            'export' => static::makeInstanceToolbarButton($id, 'Export', ['context' => 'btn-info', 'icon' => 'fa-cloud-download']),
            //'blueprint' => static::makeInstanceToolbarButton($id, 'Blueprint', ['context' => 'btn-info', 'icon' => 'fa-file-code-o']),
        ];

        if ($startStop) {
            $_buttons = array_merge($_buttons,
                [
                    'start'     => UX::makeInstanceToolbarButton($id, 'Start', ['context' => 'btn-success', 'icon' => 'fa-play',]),
                    'stop'      => UX::makeInstanceToolbarButton($id, 'Stop', ['context' => 'btn-warning', 'icon' => 'fa-stop',]),
                    'terminate' => UX::makeInstanceToolbarButton($id, 'Terminate', ['context' => 'btn-danger', 'icon' => 'fa-times',]),
                ]);
        }

        return $_buttons;
    }
}

