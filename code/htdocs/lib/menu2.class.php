<?php

	define('AUTH_LEVEL_ALL', 0);
	define('AUTH_LEVEL_ADMIN', '1');
	define('AUTH_LEVEL_USER', '2');

	define('MNU_ROOT', 0);

class Menu2
{
	var $nSelectedItem = 0;
	var $sMenuFilename = '';

	function Menu2()
	{
		global $opt;
		global $locale;

		$this->sMenuFilename = $opt['rootpath'] . 'cache2/menu-' . $locale . '.inc.php';

		require_once($this->sMenuFilename);
	}

	function setSelectedItem($tplname)
	{
		global $menuitem;

		foreach ($menuitem as $key => $item)
		{
			if (isset($item['href']) && mb_ereg_match($tplname, $item['href']))
			{
				$this->nSelectedItem = $key;

				break;
			}
		}
	}

	function getBreadcrumb()
	{
		global $menuitem;

		$retval = array();
		$retval[] = $menuitem[$this->nSelectedItem];

		$nCurItem = $this->nSelectedItem;

		while ($nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
				$retval[] = $menuitem[$nCurItem];
			}
			else
				$nCurItem = MNU_ROOT;
		}

		return array_reverse($retval);
	}

	function GetTopMenu()
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuIds();

		$retval = array();
		foreach ($menuitem[MNU_ROOT]['subitems'] AS $item)
		{
			if (($menuitem[$item]['authlevel'] == AUTH_LEVEL_ALL || $menuitem[$item]['authlevel'] == AUTH_LEVEL_USER && $login->userid || $login->admin) && $menuitem[$item]['visible'] == true)
			{
				$thisitem = $menuitem[$item];
				$thisitem['selected'] = isset($ids[$item]);
				$retval[] = $thisitem;
			}
		}

		return $retval;
	}

	function GetSubMenuItems($menuid)
	{
		global $menuitem, $login;

		$ids = $this->GetSelectedMenuItemIds($menuid);
		$topmenu = array_pop($ids);
		if (isset($menuitem[$topmenu]['parent']) && $menuitem[$topmenu]['parent'] != MNU_ROOT)
			die('internal error Menu::GetSelectedMenuIds');

		$ids[$topmenu] = $topmenu;

		$retval = array();
		if ($topmenu != MNU_ROOT)
		{
			$this->pAppendSubMenu($topmenu, $ids, $retval);
		}

		return $retval;
	}

	function pAppendSubMenu($menuid, $ids, &$items)
	{
		global $menuitem, $login;

		if (isset($menuitem[$menuid]['subitems']))
		{
			$menuIds = $this->GetSelectedMenuIds();
			foreach ($menuitem[$menuid]['subitems'] AS $item)
			{
				if (($menuitem[$item]['authlevel'] == AUTH_LEVEL_ALL || $menuitem[$item]['authlevel'] == AUTH_LEVEL_USER && $login->userid || $login->admin) && $menuitem[$item]['visible'] == true)
				{
					if (empty($menuitem[$item]['only_if_parent']) || $menuitem[$item]['only_if_parent'] && isset($menuIds[$menuitem[$item]['parent']]))
					{
						$thisitem = $menuitem[$item];
						$thisitem['selected'] = isset($ids[$item]);
						$items[] = $thisitem;

						$this->pAppendSubMenu($item, $ids, $items);
					}
				}
			}
		}
	}

	function GetSelectedMenuIds()
	{
		return $this->GetSelectedMenuItemIds($this->nSelectedItem);
	}

	function GetSelectedMenuItemIds($menuid)
	{
		global $menuitem;

		$retval = array();
		$retval[$menuid] = $menuid;

		$nCurItem = $menuid;

		while ($nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
				$retval[$nCurItem] = $nCurItem;
			}
			else
				$nCurItem = MNU_ROOT;
		}

		return $retval;
	}

	function getMenuColor()
	{
		global $menuitem;

		$nCurItem = $this->nSelectedItem;

		while (!isset($menuitem[$nCurItem]['color']) && $nCurItem != MNU_ROOT)
		{
			if (isset($menuitem[$nCurItem]['parent']))
			{
				$nCurItem = $menuitem[$nCurItem]['parent'];
			}
			else
				$nCurItem = MNU_ROOT;
		}
		if (isset($menuitem[$nCurItem]['color']))
			return $menuitem[$nCurItem]['color'];
		else
			return '';
	}

	function getMenuTitle()
	{
		global $menuitem;

		if (isset($menuitem[$this->nSelectedItem]))
		{
			return isset($menuitem[$this->nSelectedItem]['title']) ? $menuitem[$this->nSelectedItem]['title'] : '';
		}
		else
			return '';
	}

	function getTopMenuHtml()
	{
		$retVal = '';

		foreach ($this->GetTopMenu() as $menuitem)
		{
			$retVal .= '<li><a href="' . $menuitem['href'] . '"';

			if ($menuitem['selected'])
				$retVal .= ' class="selected bg-green06"';

			$retVal .= '>' . htmlspecialchars($menuitem['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>';
		}

		return $retVal;
	}

	function getSubMenuHtml($submenu)
	{
		$retVal = '';

		foreach ($submenu as $menuitem)
		{
			$retVal .= '<li class="group' . $menuitem['sublevel'];

			if ($menuitem['selected'])
				$retVal .= ' group_active';

			$retVal .= '"><a href="' . $menuitem['href'] . '">' . htmlspecialchars($menuitem['menustring'], ENT_COMPAT, 'UTF-8') . '</a></li>';
		}

		return $retVal;
	}

	function getMenuItem($tplname)
	{
		global $menuitem;

		foreach ($menuitem as $item)
		{
			if (mb_ereg_match($tplname, $item['href']))
				return $item;
		}

		return null;
	}

	function getBreadCrumbHtml()
	{
		$breadCrumb = '';
		$breadCrumbs = $this->getBreadcrumb();
		$count = count($breadCrumbs);

		for ($i = 0; $i < $count; $i++)
		{
			if (!isset($breadCrumbs[$i]['menustring']))
				continue;

			if ($i > 0)
				$breadCrumb .= '&nbsp;&gt;&nbsp;';

			if ($i < $count - 1)
				$breadCrumb .= '<a href="' . $breadCrumbs[$i]['href'] . '">';

			$breadCrumb .= htmlspecialchars($breadCrumbs[$i]['menustring'], ENT_COMPAT, 'UTF-8');

			if ($i < $count - 1)
				$breadCrumb .='</a>';
		}

		return $breadCrumb;
	}
}
?>