{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
{* OCSTYLE *}
{strip}
	{foreach name=breadcrumb from=$breadcrumb item=menuitem}
		{if !$smarty.foreach.breadcrumb.first}&nbsp;&gt;&nbsp;{/if}
		<a href="{$menuitem.href}">{$menuitem.menustring|escape}</a>
	{/foreach}
{/strip}