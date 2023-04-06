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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

if (!dcCore::app()->newVersion(basename(__DIR__), dcCore::app()->plugins->moduleInfo(basename(__DIR__), 'version'))) {
    return;
}

try {
    // Default state is active for fullscreen
    dcCore::app()->auth->user_prefs->interface->put('zenedit_fullscreen', 1, 'boolean', 'Try to activate full screen in zen mode', false, true);
    dcCore::app()->auth->user_prefs->interface->put('zenedit_background', '', 'string', 'Background image in zen mode', false, true);
    dcCore::app()->auth->user_prefs->interface->put('zenedit_small_margins', 0, 'boolean', 'Try to activate full screen in zen mode', false, true);

    return true;
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

return false;
