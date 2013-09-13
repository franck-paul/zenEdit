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

class zenEditBehaviors
{
	public static function jsLoad()
	{
		return
		'<script type="text/javascript">'."\n".
		"//<![CDATA[\n".
		dcPage::jsVar('dotclear.msg.zenEditShow',__('Switch to zen mode')).
		dcPage::jsVar('dotclear.msg.zenEditHide',__('Exit from zen mode')).
		dcPage::jsVar('dotclear.zenMode_FullScreen','0').
		dcPage::jsVar('dotclear.zenMode','0').
		"\n//]]>\n".
		"</script>\n".
		'<script type="text/javascript" src="index.php?pf=zenEdit/js/post.js"></script>'."\n";
	}
}
?>