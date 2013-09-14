<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of zenEdit, a plugin for Dotclear 2.
#
# Copyright (c) Franck Paul
# carnet.franck.paul@gmail.com
#
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_CONTEXT_ADMIN')) { return; }

$core->addBehavior('adminPostHeaders',array('zenEditBehaviors','jsLoad'));
$core->addBehavior('adminPageHeaders',array('zenEditBehaviors','jsLoad'));
$core->addBehavior('adminRelatedHeaders',array('zenEditBehaviors','jsLoad'));

$core->addBehavior('adminBeforeUserOptionsUpdate',array('zenEditBehaviors','adminBeforeUserUpdate'));
$core->addBehavior('adminPreferencesForm',array('zenEditBehaviors','adminPreferencesForm'));

class zenEditBehaviors
{
	public static function jsLoad()
	{
		global $core;

		$core->auth->user_prefs->addWorkspace('interface');
		$full_screen = $core->auth->user_prefs->interface->zenedit_fullscreen ? '1' : '0';

		return
		'<script type="text/javascript">'."\n".
		"//<![CDATA[\n".
		dcPage::jsVar('dotclear.msg.zenEditShow',__('Switch to zen mode')).
		dcPage::jsVar('dotclear.msg.zenEditHide',__('Exit from zen mode')).
		dcPage::jsVar('dotclear.zenMode_FullScreen',$full_screen).
		dcPage::jsVar('dotclear.zenMode','0').
		"\n//]]>\n".
		"</script>\n".
		'<script type="text/javascript" src="index.php?pf=zenEdit/js/post.js"></script>'."\n";
	}

	public static function adminBeforeUserUpdate($cur,$userID)
	{
		global $core;

		// Get and store user's prefs for plugin options
		$core->auth->user_prefs->addWorkspace('interface');
		try {
			$core->auth->user_prefs->interface->put('zenedit_fullscreen',!empty($_POST['zenedit_fullscreen']),'boolean');
		}
		catch (Exception $e)
		{
			$core->error->add($e->getMessage());
		}
	}

	public static function adminPreferencesForm($core)
	{
		global $core;

		// Add fieldset for plugin options
		$core->auth->user_prefs->addWorkspace('interface');

		echo
		'<p><label for="zenedit_fullscreen" class="classic">'.
		form::checkbox('zenedit_fullscreen',1,$core->auth->user_prefs->interface->zenedit_fullscreen).'</label>'.
		__('Try to activate full screen in editor\'s zen mode').'</p>'.
		'<p class="clear form-note">'.__('Your browser may not support this feature or it may be deactivated by the system.').'</p>';
	}
}
?>