{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{strip}
	{foreach name=breadcrumb from=$breadcrumb item=menuitem}
		{if !$smarty.foreach.breadcrumb.first}&nbsp;&gt;&nbsp;{/if}
		{if !$smarty.foreach.breadcrumb.last}<a href="{$menuitem.href}">{/if}
		{$menuitem.menustring|escape}
		{if !$smarty.foreach.breadcrumb.last}</a>{/if}
	{/foreach}
{/strip}