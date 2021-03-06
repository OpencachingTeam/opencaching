<?php
/***************************************************************************
											./lang/de/ocstyle/editlog.inc.php
															-------------------
		begin                : Mon July 5 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 language vars

 ****************************************************************************/

	$submit = t('Change');

 	$error_wrong_node = t('This log entry has been created on another Opencaching website. The cache can only be edited there.');

	$date_message = '<span class="errormsg">' . t('date is invalid, format: TT-MM-JJJJ') . '</span>';
	$smiley_link = '<a href="javascript:insertSmiley(\'{smiley_text}\')">{smiley_image}</a>';

	$log_pw_field = '<tr><td colspan="2">' . t('passwort to log:') . ' <input class="input100" type="text" name="log_pw" maxlength="20" value="" /> ' . t('(only for found logs)') . '</td></tr>
					<tr><td class="spacer" colspan="2"></td></tr>';
	$log_pw_field_pw_not_ok = '<tr><td colspan="2">' . t('passwort to log:') . ' <input type="text" name="log_pw" maxlength="20" size="20" value=""/><span class="errormsg"> ' . t('Invalid password!') . '</span></td></tr><tr><td class="spacer" colspan="2"></td></tr>';
?>