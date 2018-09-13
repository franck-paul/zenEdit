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

if (!defined('DC_CONTEXT_ADMIN')) {return;}

// dead but useful code, in order to have translations
__('zenEdit') . __('Zen mode for dcLegacyEditor');

$core->addBehavior('adminPostEditor', ['zenEditBehaviors', 'adminPostEditor']);

$core->addBehavior('adminBeforeUserOptionsUpdate', ['zenEditBehaviors', 'adminBeforeUserUpdate']);
$core->addBehavior('adminPreferencesHeaders', ['zenEditBehaviors', 'adminPreferencesHeaders']);
$core->addBehavior('adminPreferencesForm', ['zenEditBehaviors', 'adminPreferencesForm']);

class zenEditBehaviors
{
    public static function adminPostEditor($editor = '', $context = '', array $tags = [], $syntax = '')
    {
        if ($editor != 'dcLegacyEditor') {
            return;
        }

        global $core;

        $core->auth->user_prefs->addWorkspace('interface');
        $full_screen   = $core->auth->user_prefs->interface->zenedit_fullscreen ? '1' : '0';
        $background    = $core->auth->user_prefs->interface->zenedit_background;
        $small_margins = $core->auth->user_prefs->interface->zenedit_small_margins ? '1' : '0';

        return
        '<script type="text/javascript">' . "\n" .
        dcPage::jsVar('dotclear.msg.zenEditShow', __('Switch to zen mode')) .
        dcPage::jsVar('dotclear.msg.zenEditHide', __('Exit from zen mode')) .
        dcPage::jsVar('dotclear.zenMode_FullScreen', $full_screen) .
        dcPage::jsVar('dotclear.zenMode_Background', $background) .
        dcPage::jsVar('dotclear.zenMode_SmallMargins', $small_margins) .
        dcPage::jsVar('dotclear.zenMode', '0') .
        "</script>\n" .
        dcPage::jsLoad(urldecode(dcPage::getPF('zenEdit/js/post.js')), $core->getVersion('zenEdit'));
    }

    public static function adminBeforeUserUpdate($cur, $userID)
    {
        global $core;

        // Get and store user's prefs for plugin options
        $core->auth->user_prefs->addWorkspace('interface');
        try {
            $core->auth->user_prefs->interface->put('zenedit_fullscreen', !empty($_POST['zenedit_fullscreen']), 'boolean');
            $core->auth->user_prefs->interface->put('zenedit_background', (!empty($_POST['zenedit_background']) ? $_POST['zenedit_background'] : ''));
            $core->auth->user_prefs->interface->put('zenedit_small_margins', !empty($_POST['zenedit_small_margins']), 'boolean');
        } catch (Exception $e) {
            $core->error->add($e->getMessage());
        }
    }

    public static function adminPreferencesHeaders()
    {
        global $core;

        return
        dcPage::jsLoad(urldecode(dcPage::getPF('zenEdit/js/preferences.js')), $core->getVersion('zenEdit')) .
        dcPage::cssLoad(urldecode(dcPage::getPF('zenEdit/style.css')), 'screen', $core->getVersion('zenEdit'));
    }

    public static function adminPreferencesForm($core)
    {
        $textures_combo       = [__('None') => ''];
        $textures_combo_dark  = [];
        $textures_combo_light = [];
        // Light textures
        $textures_root = dirname(__FILE__) . '/img/background/light/';
        if (is_dir($textures_root) && is_readable($textures_root)) {
            if (($d = @dir($textures_root)) !== false) {
                while (($entry = $d->read()) !== false) {
                    if ($entry != '.' && $entry != '..' && substr($entry, 0, 1) != '.' && is_readable($textures_root . '/' . $entry)) {
                        $textures_combo_light[substr($entry, 0, -4)] = 'light/' . $entry;
                    }
                }
                if (count($textures_combo_light) > 0) {
                    $textures_combo[__('Light backgrounds')] = $textures_combo_light;
                }
            }
        }
        // Dark textures
        $textures_root = dirname(__FILE__) . '/img/background/dark/';
        if (is_dir($textures_root) && is_readable($textures_root)) {
            if (($d = @dir($textures_root)) !== false) {
                while (($entry = $d->read()) !== false) {
                    if ($entry != '.' && $entry != '..' && substr($entry, 0, 1) != '.' && is_readable($textures_root . '/' . $entry)) {
                        $textures_combo_dark[substr($entry, 0, -4)] = 'dark/' . $entry;
                    }
                }
                if (count($textures_combo_dark) > 0) {
                    $textures_combo[__('Dark backgrounds')] = $textures_combo_dark;
                }
            }
        }

        // Add fieldset for plugin options
        $core->auth->user_prefs->addWorkspace('interface');
        $background = $core->auth->user_prefs->interface->zenedit_background;

        echo
        '<div class="fieldset">' .
        '<h5>' . __('Zen mode for dcLegacyEditor') . '</h5>';
        echo
        '<p><label for="zenedit_fullscreen" class="classic">' .
        form::checkbox('zenedit_fullscreen', 1, $core->auth->user_prefs->interface->zenedit_fullscreen) . '</label>' .
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
        form::checkbox('zenedit_small_margins', 1, $core->auth->user_prefs->interface->zenedit_small_margins) . '</label>' .
        __('Small margins (useful on small screens)') . '</p>';
        echo '</div>';
    }
}
