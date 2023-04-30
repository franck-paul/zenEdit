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

class Backend extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = defined('DC_CONTEXT_ADMIN');

        // dead but useful code, in order to have translations
        __('zenEdit') . __('Zen mode for dcLegacyEditor');

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        dcCore::app()->addBehaviors([
            'adminPostEditor' => [BackendBehaviors::class, 'adminPostEditor'],

            'adminBeforeUserOptionsUpdate' => [BackendBehaviors::class, 'adminBeforeUserUpdate'],
            'adminPreferencesHeaders'      => [BackendBehaviors::class, 'adminPreferencesHeaders'],
            'adminPreferencesFormV2'       => [BackendBehaviors::class, 'adminPreferencesForm'],
        ]);

        return true;
    }
}
