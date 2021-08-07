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

$new_version = $core->plugins->moduleInfo('zenEdit', 'version');
$old_version = $core->getVersion('zenEdit');

if (version_compare($old_version, $new_version, '>=')) {
    return;
}

try {
    // Default state is active for fullscreen
    $core->auth->user_prefs->addWorkspace('interface');
    $core->auth->user_prefs->interface->put('zenedit_fullscreen', 1, 'boolean', 'Try to activate full screen in zen mode', false, true);
    $core->auth->user_prefs->interface->put('zenedit_background', '', 'string', 'Background image in zen mode', false, true);
    $core->auth->user_prefs->interface->put('zenedit_small_margins', 0, 'boolean', 'Try to activate full screen in zen mode', false, true);

    $core->setVersion('zenEdit', $new_version);

    return true;
} catch (Exception $e) {
    $core->error->add($e->getMessage());
}

return false;
