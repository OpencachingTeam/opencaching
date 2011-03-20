<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib2/translateAccess.php');

function createTranslate()
{
	$access = new translateAccess();

	if ($access->hasAccess())
	{
		global $cookie;

		$translateMode = $cookie->get('translate_mode');

		if ($translateMode)
			return new translateEdit($translateMode == 'all');
	}

	return new translate();
}

$translate = createTranslate();

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
	protected function prepare_text($text)
	{
		$text = mb_ereg_replace("\t", ' ', $text);
		$text = mb_ereg_replace("\r", ' ', $text);
		$text = mb_ereg_replace("\n", ' ', $text);
		while (mb_strpos($text, '  ') !== false)
			$text = mb_ereg_replace('  ', ' ', $text);

		return $text;
	}
}

class translateEdit extends translate
{
	private $editAll;
	
	public function __construct($editAll = true)
	{
		$this->editAll = $editAll;
	}

	function t($message, $style, $resource_name, $line, $plural='', $count=1)
	{
		global $opt;

		if ($message == '')
			return '';

		if ($message == 'INTERNAL_LANG')
			return parent::t($message, $style, $resource_name, $line, $plural, $count);

		if ($plural != '' && $count!=1)
			$message = $plural;

		$search = $this->prepare_text($message);

		$rs = sql("SELECT `sys_trans`.`id` , `sys_trans_text`.`text` FROM `sys_trans` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id` = `sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang` = 'SV' WHERE `sys_trans`.`text` = '&1'", $search);
		$r = sql_fetch_assoc($rs);

		if ($r['text'] && !$this->editAll)
			 return $r['text'];

		global $lang;

		$text = $r['text'] ? $r['text'] : gettext($search);

		return $text . ' <a href= translate.php?action=edit&id=' . $r['id'] . '>Edit</a>';
	}
}

?>