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
declare(strict_types=1);

namespace Dotclear\Plugin\zenEdit;

use dcCore;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN')
            && My::phpCompliant()
            && dcCore::app()->newVersion(My::id(), dcCore::app()->plugins->moduleInfo(My::id(), 'version'));

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Default state is active for fullscreen
            $preferences = dcCore::app()->auth->user_prefs->interface;
            $preferences->put('zenedit_fullscreen', 1, 'boolean', 'Try to activate full screen in zen mode', false, true);
            $preferences->put('zenedit_background', '', 'string', 'Background image in zen mode', false, true);
            $preferences->put('zenedit_small_margins', 0, 'boolean', 'Try to activate full screen in zen mode', false, true);

            return true;
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
