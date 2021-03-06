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

if (!defined('DC_RC_PATH')) {return;}

$this->registerModule(
    "zenEdit",                     // Name
    "Zen mode for dcLegacyEditor", // Description
    "Franck Paul",                 // Author
    '0.7',                         // Version
    [
        'requires'    => [['core', '2.16']],                       // Dependencies
        'permissions' => 'usage,contentadmin',                     // Permissions
        'type'        => 'plugin',                                 // Type
        'details'     => 'https://open-time.net/?q=zenEdit',       // Details URL
        'support'     => 'https://github.com/franck-paul/zenEdit', // Support URL
        'settings'    => [
            'pref' => '#user-options.zenEdit_prefs'
        ]
    ]
);
