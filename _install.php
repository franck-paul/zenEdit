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

$new_version = $core->plugins->moduleInfo('zenEdit','version');
$old_version = $core->getVersion('zenEdit');

if (version_compare($old_version,$new_version,'>=')) return;

try
{
	// Default state is active for fullscreen
	$core->auth->user_prefs->addWorkspace('interface');
	$core->auth->user_prefs->interface->put('zenedit_fullscreen',1,'boolean','Try to activate full screen in zen mode',false,true);
	$core->auth->user_prefs->interface->put('zenedit_background','','string','Background image in zen mode',false,true);

	$core->setVersion('zenEdit',$new_version);

	return true;
}
catch (Exception $e)
{
	$core->error->add($e->getMessage());
}
return false;
?>