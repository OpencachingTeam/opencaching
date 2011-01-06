<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *  Display some status information about the server and Opencaching
 ***************************************************************************/

	require('./lib2/web.inc.php');

	// Parameter
	$jpeg_qualitaet = 80;
	$fontfile = $opt['rootpath'] . "lib2/fonts/dejavu-fonts/ttf/DejaVuSans.ttf";

	// get userid and style from URL
	$userid = isset($_REQUEST['userid']) ? $_REQUEST['userid']+0 : 0;

	if (!file_exists($opt['rootpath'] . 'images/statpics/statpic'.$userid.'.jpg') ||
	    sql_value("SELECT COUNT(*) FROM `user_statpic` WHERE `user_id`='&1'", 0, $userid) == 0)
	{
		// get detailed info from DB
		$rs = sql("SELECT `user`.`username`, `stat_user`.`hidden`, `stat_user`.`found`, `user`.`statpic_logo`, `user`.`statpic_text` FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE `user`.`user_id`='&1'", $userid);
		if (sql_num_rows($rs) == 1)
		{
			$record = sql_fetch_array($rs);
			$username = $record['username'];
			$found = isset($record['found']) ? $record['found'] : 0;
			$hidden = isset($record['hidden']) ? $record['hidden'] : 0;
			$logo = isset($record['statpic_logo']) ? $record['statpic_logo'] : 0;
			$logotext = isset($record['statpic_text']) ? $record['statpic_text'] : 'Opencaching';
		}
		else
		{
			$userid = 0;
			$username = "<User not known>";
			$found = 0;
			$hidden = 0;
			$logo = 0;
			$logotext = 'Opencaching';

		}
		sql_free_result($rs);

		if ($userid == 0)
			if (file_exists($opt['rootpath'] . 'images/statpics/statpic'.$userid.'.jpg'))
				$tpl->redirect('images/statpics/statpic'.$userid.'.jpg');

		// Bild existiert nicht => neu erstellen
		$rs = sql("SELECT `tplpath`, `maxtextwidth` FROM `statpics` WHERE `id`='&1'", $logo);

		if (sql_num_rows($rs) == 1)
		{
			$record = sql_fetch_array($rs);
			$tplpath = $opt['rootpath'] . $record['tplpath'];
			$maxtextwidth = $record['maxtextwidth'];
		}
		else
		{
			$tplpath = $opt['rootpath'] . 'images/ocstats1.gif';
			$maxtextwidth = 60;
			$logo = 1;
		}
		sql_free_result($rs);

		$im = ImageCreateFromGIF($tplpath);
		$clrWhite = ImageColorAllocate($im, 255, 255, 255);
		$clrBorder = ImageColorAllocate($im, 70, 70, 70);
		$clrBlack = ImageColorAllocate($im, 0, 0, 0);
		$clrBlue = ImageColorAllocate($im, 0, 0, 255);

		switch ($logo)
		{
		case 4:
		case 5:
			// write text
			$fontsize = 10;
			$text = $username;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
			$fontsize = 7.5;
			$text = "Gefunden: $found  Versteckt: $hidden";
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 32, $clrBlack, $fontfile, $text);
			break;
		case 2:
			// write text
			$fontsize = 10;
			$text = $username;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
			$fontsize = 7;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 29, $clrBlack, $fontfile, $logotext);
			$fontsize = 7.5;
			$text = "Gefunden: $found  Versteckt: $hidden";
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 45, $clrBlack, $fontfile, $text);
			break;
		case 6:
		case 7:
			// write text
			$fontsize = 10;
			$text = $username;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
			$fontsize = 7.5;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 32, $clrBlack, $fontfile, $logotext);
			break;
		case 1:
		default:
			// write text
			$fontsize = 10;
			$text = $username;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 15, $clrBlack, $fontfile, $text);
			$fontsize = 7;
			$text = "Gefunden: $found  Versteckt: $hidden";
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $text);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 29, $clrBlack, $fontfile, $text);
			$fontsize = 8;
			$textsize = imagettfbbox($fontsize, 0, $fontfile, $logotext);
			ImageTTFText($im, $fontsize, 0, (imagesx($im)-($textsize[2]-$textsize[0])-5 > $maxtextwidth) ? imagesx($im)-($textsize[2]-$textsize[0])-5 : $maxtextwidth, 45, $clrBlack, $fontfile, $logotext);
		}

		// draw border
		ImageRectangle($im, 0, 0, imagesx($im)-1, imagesy($im)-1, $clrBorder);
		// write output
		Imagejpeg($im, $opt['rootpath'] . 'images/statpics/statpic'.$userid.'.jpg', $jpeg_qualitaet);
		ImageDestroy($im);

		sql("INSERT INTO `user_statpic` (`user_id`) VALUES ('&1') ON DUPLICATE KEY UPDATE `date_created`=NOW()", $userid);
	}

	// Redirect auf das gespeicherte Bild
	$tpl->redirect('images/statpics/statpic'.$userid.'.jpg');
?>