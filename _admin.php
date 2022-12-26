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

// dead but useful code, in order to have translations
__('zenEdit') . __('Zen mode for dcLegacyEditor');

class zenEditBehaviors
{
    public static function adminPostEditor($editor = ''): string
    {
        if ($editor !== 'dcLegacyEditor') {
            return '';
        }

        dcCore::app()->auth->user_prefs->addWorkspace('interface');
        $full_screen   = dcCore::app()->auth->user_prefs->interface->zenedit_fullscreen ? '1' : '0';
        $background    = dcCore::app()->auth->user_prefs->interface->zenedit_background;
        $small_margins = dcCore::app()->auth->user_prefs->interface->zenedit_small_margins ? '1' : '0';

        return
        dcPage::jsJson('zenedit', [
            'msg' => [
                'zenEdit' => [
                    'show' => __('Switch to zen mode'),
                    'hide' => __('Exit from zen mode'),
                ],
            ],
            'zenEdit' => [
                'fullScreen'   => $full_screen,
                'background'   => $background,
                'smallMargins' => $small_margins,
                'mode'         => 0,
            ],
        ]) .
        dcPage::jsModuleLoad('zenEdit/js/post.js', dcCore::app()->getVersion('zenEdit'));
    }

    public static function adminBeforeUserUpdate()
    {
        // Get and store user's prefs for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('interface');

        try {
            dcCore::app()->auth->user_prefs->interface->put('zenedit_fullscreen', !empty($_POST['zenedit_fullscreen']), 'boolean');
            dcCore::app()->auth->user_prefs->interface->put('zenedit_background', (!empty($_POST['zenedit_background']) ? $_POST['zenedit_background'] : ''));
            dcCore::app()->auth->user_prefs->interface->put('zenedit_small_margins', !empty($_POST['zenedit_small_margins']), 'boolean');
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }

    public static function adminPreferencesHeaders(): string
    {
        return
        dcPage::jsModuleLoad('zenEdit/js/preferences.js', dcCore::app()->getVersion('zenEdit')) .
        dcPage::cssModuleLoad('zenEdit/css/style.css', 'screen', dcCore::app()->getVersion('zenEdit'));
    }

    public static function adminPreferencesForm()
    {
        $textures_combo       = [__('None') => ''];
        $textures_combo_dark  = [];
        $textures_combo_light = [];
        // Light textures
        $textures_root = __DIR__ . '/img/background/light/';
        if (is_dir($textures_root) && is_readable($textures_root) && ($d = @dir($textures_root)) !== false) {
            while (($entry = $d->read()) !== false) {
                if ($entry != '.' && $entry != '..' && substr($entry, 0, 1) != '.' && is_readable($textures_root . '/' . $entry)) {
                    $textures_combo_light[substr($entry, 0, -4)] = 'light/' . $entry;
                }
            }
            if (count($textures_combo_light)) {
                $textures_combo[__('Light backgrounds')] = $textures_combo_light;
            }
        }
        // Dark textures
        $textures_root = __DIR__ . '/img/background/dark/';
        if (is_dir($textures_root) && is_readable($textures_root) && ($d = @dir($textures_root)) !== false) {
            while (($entry = $d->read()) !== false) {
                if ($entry != '.' && $entry != '..' && substr($entry, 0, 1) != '.' && is_readable($textures_root . '/' . $entry)) {
                    $textures_combo_dark[substr($entry, 0, -4)] = 'dark/' . $entry;
                }
            }
            if (count($textures_combo_dark)) {
                $textures_combo[__('Dark backgrounds')] = $textures_combo_dark;
            }
        }

        // Add fieldset for plugin options
        dcCore::app()->auth->user_prefs->addWorkspace('interface');
        $background = dcCore::app()->auth->user_prefs->interface->zenedit_background;

        echo
        '<div class="fieldset">' .
        '<h5 id="zenEdit_prefs">' . __('Zen mode for dcLegacyEditor') . '</h5>';
        echo
        '<p><label for="zenedit_fullscreen" class="classic">' .
        form::checkbox('zenedit_fullscreen', 1, dcCore::app()->auth->user_prefs->interface->zenedit_fullscreen) . '</label>' .
        __('Try to activate full screen in editor\'s zen mode') . '</p>' .
        '<p class="clear form-note">' . __('Your browser may not support this feature or it may be deactivated by the system.') . '</p>';
        if (count($textures_combo) > 1) {
            echo
            '<p><label for="zenedit_background" class="classic">' . __('Background:') . '</label> ' .
            form::combo('zenedit_background', $textures_combo, $background) . '</p>' .
            ' <span id="zenedit_sample" class="fieldset" style="background-image:url(' .
            urldecode(dcPage::getPF('zenEdit/img/background/' . $background)) . ')">&nbsp;</span>';
        } else {
            echo form::hidden('zenedit_background', '');
        }
        echo
        '<p><label for="zenedit_small_margins" class="classic">' .
        form::checkbox('zenedit_small_margins', 1, dcCore::app()->auth->user_prefs->interface->zenedit_small_margins) . '</label>' .
        __('Small margins (useful on small screens)') . '</p>';
        echo '</div>';
    }
}

dcCore::app()->addBehavior('adminPostEditor', [zenEditBehaviors::class, 'adminPostEditor']);

dcCore::app()->addBehavior('adminBeforeUserOptionsUpdate', [zenEditBehaviors::class, 'adminBeforeUserUpdate']);
dcCore::app()->addBehavior('adminPreferencesHeaders', [zenEditBehaviors::class, 'adminPreferencesHeaders']);
dcCore::app()->addBehavior('adminPreferencesFormV2', [zenEditBehaviors::class, 'adminPreferencesForm']);
