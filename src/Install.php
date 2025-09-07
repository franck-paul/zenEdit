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

use Dotclear\App;
use Dotclear\Helper\Process\TraitProcess;
use Exception;

class Install
{
    use TraitProcess;

    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Default state is active for fullscreen
            $preferences = App::auth()->prefs();
            $preferences->interface->put('zenedit_fullscreen', 1, 'boolean', 'Try to activate full screen in zen mode', false, true);
            $preferences->interface->put('zenedit_background', '', 'string', 'Background image in zen mode', false, true);
            $preferences->interface->put('zenedit_small_margins', 0, 'boolean', 'Try to activate full screen in zen mode', false, true);
        } catch (Exception $exception) {
            App::error()->add($exception->getMessage());
        }

        return true;
    }
}
