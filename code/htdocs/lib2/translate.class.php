<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

require_once('./lib2/translateAccess.php');
require_once('./lib2/translationHandler.class.php');

function createTranslate($backtrace_level = 0)
{
	$access = new translateAccess();

	if ($access->hasAccess())
	{
		global $cookie;

		$translateMode = $cookie->get('translate_mode');

		if ($translateMode)
			return new translateEdit($translateMode == 'all', $backtrace_level);
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
	private $backtrace_level;

	public function __construct($editAll = true, $backtrace_level = 0)
	{
		$this->editAll = $editAll;
		$this->backtrace_level = $backtrace_level;
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
		$language = $opt['template']['locale'];

		if (!isset($language))
		{
			global $locale;

			$language = $locale;
		}

		$rs = sql("SELECT `sys_trans`.`id` , `sys_trans_text`.`text` FROM `sys_trans` LEFT JOIN `sys_trans_text` ON `sys_trans`.`id` = `sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang` = '&1' WHERE `sys_trans`.`text` = '&2'", $language, $search);
		$r = sql_fetch_assoc($rs);

		if ($r['text'] && !$this->editAll)
			 return $r['text'];

		if (empty($r['id']))
		{
			global $translationHandler;

			if (empty($resource_name))
			{
				$backtrace = debug_backtrace();
				$item = $backtrace[$this->backtrace_level];
				$resource_name = $item['file'];
				$line = $item['line'];
			}

			$translationHandler->addText($search, $resource_name, $line);

			return $this->t($message, $style, $resource_name, $line, $plural, $count);
		}

		$text = $r['text'] ? $r['text'] : gettext($search);

		return $text . ' <a href= translate.php?action=edit&id=' . $r['id'] . '>Edit</a>';
	}
}

?>