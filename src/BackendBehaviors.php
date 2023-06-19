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
use dcPage;
use Dotclear\Helper\Html\Form\Checkbox;
use Dotclear\Helper\Html\Form\Fieldset;
use Dotclear\Helper\Html\Form\Hidden;
use Dotclear\Helper\Html\Form\Label;
use Dotclear\Helper\Html\Form\Legend;
use Dotclear\Helper\Html\Form\Para;
use Dotclear\Helper\Html\Form\Select;
use Dotclear\Helper\Html\Form\Text;
use Exception;

class BackendBehaviors
{
    public static function adminPostEditor($editor = ''): string
    {
        if ($editor !== 'dcLegacyEditor') {
            return '';
        }

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
                'icon'         => urldecode(dcPage::getPF(My::id() . '/icon.svg')),
                'base_url'     => urldecode(dcPage::getPF(My::id() . '/img/background/')),
            ],
        ]) .
        dcPage::jsModuleLoad(My::id() . '/js/post.js', dcCore::app()->getVersion(My::id()));
    }

    public static function adminBeforeUserUpdate()
    {
        // Get and store user's prefs for plugin options
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
        dcPage::jsJson('zenedit_prefs', [
            'base_url' => urldecode(dcPage::getPF(My::id() . '/img/background/')),
        ]) .
        dcPage::jsModuleLoad(My::id() . '/js/preferences.js', dcCore::app()->getVersion(My::id())) .
        dcPage::cssModuleLoad(My::id() . '/css/style.css', 'screen', dcCore::app()->getVersion(My::id()));
    }

    public static function adminPreferencesForm()
    {
        $textures_combo = [__('None') => ''];

        $textures_combo_dark  = [];
        $textures_combo_light = [];

        $base_path = implode(DIRECTORY_SEPARATOR, [My::path(), 'img', 'background']);

        // Light textures
        $textures_root = implode(DIRECTORY_SEPARATOR, [$base_path, 'light']);
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
        $textures_root = implode(DIRECTORY_SEPARATOR, [$base_path, 'dark']);
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
        $background = dcCore::app()->auth->user_prefs->interface->zenedit_background;

        // Prepare texture selector
        if (count($textures_combo) > 1) {
            $textures = [
                (new Para())->items([
                    (new Select('zenedit_background'))
                    ->items($textures_combo)
                    ->default($background)
                    ->label((new Label(__('Background:'), Label::INSIDE_TEXT_BEFORE))),
                ]),
                (new Text(null, ' ')),
                (new Text('span', '&nbsp;'))
                    ->id('zenedit_sample')
                    ->class('fieldset')
                    ->extra('style="background-image:url(' . urldecode(dcPage::getPF(My::id() . '/img/background/' . $background)) . ')"'),
            ];
        } else {
            $textures = [(new Hidden('zenedit_background', ''))];
        }

        echo
        (new Fieldset('zenEdit_prefs'))
        ->legend((new Legend(__('Zen mode for dcLegacyEditor'))))
        ->fields([
            (new Para())->items([
                (new Checkbox('zenedit_fullscreen', dcCore::app()->auth->user_prefs->interface->zenedit_fullscreen))
                    ->value(1)
                    ->label((new Label(__('Try to activate full screen in editor\'s zen mode'), Label::INSIDE_TEXT_AFTER))),
            ]),
            (new Para())->items([
                (new Text(null, __('Your browser may not support this feature or it may be deactivated by the system.')))
                    ->class(['clear', 'form-note']),
            ]),
            ...$textures,   // See above
            (new Para())->items([
                (new Checkbox('zenedit_small_margins', dcCore::app()->auth->user_prefs->interface->zenedit_small_margins))
                    ->value(1)
                    ->label((new Label(__('Small margins (useful on small screens)'), Label::INSIDE_TEXT_AFTER))),
            ]),
        ])
        ->render();
    }
}
