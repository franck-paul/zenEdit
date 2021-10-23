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
if (!defined('DC_RC_PATH')) {
    return;
}

$this->registerModule(
    'zenEdit',                     // Name
    'Zen mode for dcLegacyEditor', // Description
    'Franck Paul',                 // Author
    '0.8',                         // Version
    [
        'requires'    => [['core', '2.19']],                       // Dependencies
        'permissions' => 'usage,contentadmin',                     // Permissions
        'type'        => 'plugin',                                 // Type
        'settings'    => [
            'pref' => '#user-options.zenEdit_prefs'
        ],

        'details'    => 'https://open-time.net/?q=zenEdit',       // Details URL
        'support'    => 'https://github.com/franck-paul/zenEdit', // Support URL
        'repository' => 'https://raw.githubusercontent.com/franck-paul/zenEdit/main/dcstore.xml'
    ]
);
