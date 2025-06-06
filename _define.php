<?php

/**
 * @brief zenEdit, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
$this->registerModule(
    'zenEdit',
    'Zen mode for dcLegacyEditor',
    'Franck Paul',
    '6.1',
    [
        'date'        => '2025-06-05T14:37:58+0200',
        'requires'    => [['core', '2.34']],
        'permissions' => 'My',
        'type'        => 'plugin',
        'priority'    => 1010, // Must be higher than dcLegacyEditor/dcCKEditor priority (ie 1000)
        'settings'    => [
            'pref' => '#user-options.zenEdit_prefs',
        ],

        'details'    => 'https://open-time.net/?q=zenEdit',
        'support'    => 'https://github.com/franck-paul/zenEdit',
        'repository' => 'https://raw.githubusercontent.com/franck-paul/zenEdit/main/dcstore.xml',
        'license'    => 'gpl2',
    ]
);
