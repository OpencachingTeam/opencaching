<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

$translate = new translate();

class translate
{
	/* translate the given string
	 */
	function t($message, $style, $resource_name, $line, $plural='', $count=1)
	{
		global $opt;

		if ($message == '')
			return '';

		if ($plural != '' && $count!=1)
			$message = $plural;
		$search = $this->prepare_text($message);

		return gettext($search);
	}

	/* strip whitespaces
	 */
	private function prepare_text($text)
	{
		$text = mb_ereg_replace("\t", ' ', $text);
		$text = mb_ereg_replace("\r", ' ', $text);
		$text = mb_ereg_replace("\n", ' ', $text);
		while (mb_strpos($text, '  ') !== false)
			$text = mb_ereg_replace('  ', ' ', $text);

		return $text;
	}
}
?>