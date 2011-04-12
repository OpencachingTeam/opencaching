{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<script type="text/javascript">
<!--
{literal}
	function checkForm()
	{
		if (document.fpodcast.title.value == "")
		{
			alert('{/literal}{t escape=js}Give the podcast a name!{/t}{literal}');
			return false;
		}
		return true;
	}
{/literal}
//-->
</script>

<form action="podcast.php" method="post" enctype="multipart/form-data" name="fpodcast" dir="ltr" onsubmit="return checkForm();">
	<input type="hidden" name="action" value="{$action|escape}" />
	{if $action=='add'}
		<input type="hidden" name="cacheuuid" value="{$cacheuuid|escape}" />
	{else}
		<input type="hidden" name="uuid" value="{$uuid|escape}" />
	{/if}

	<div class="content2-pagetitle">
		<img src="resource2/{$opt.template.style}/images/description/22x22-image.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="{t}Edit podcast{/t}" title="{t}Edit podcast{/t}" />
		{capture name="name"}
			<a href="viewcache.php?wp={$cachewp|escape}">{$cachename|escape}</a>
		{/capture}

		{if $action=='add'}
			{t 1=$smarty.capture.name}Add podcast for Geocaches %1{/t}
		{else}
			{t 1=$smarty.capture.name}Edit podcast for Geocaches %1{/t}
		{/if}
	</div>

	<table class="table">
		<tr>
			<td valign="top">{t}Name:{/t}</td>
			<td>
				<input class="input200" name="title" type="text" value="{$title|escape}" size="43" />
				{if $errortitle==true}
					<span class="errormsg">{t}Give the podcast a name!{/t}</span>
				{/if}
			</td>
		</tr>
		{if $action=='add'}
			<tr>
				<td valign="top">{t}File:{/t}</td>
				<td>
					<input type="hidden" name="MAX_FILE_SIZE" value="{$opt.logic.podcasts.maxsize}">
					<input class="input200" name="file" type="file" maxlength="{$opt.logic.podcasts.maxsize}" />
				</td>
			</tr>
			{if $errorfile==ERROR_UPLOAD_ERR_NO_FILE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}No podcast file given.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_ERR_SIZE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}The file was too big. The maximum file size is 1500 KB.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_UNKNOWN}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}The file was not uploaded correctly.{/t}</span></td></tr>
			{elseif $errorfile==ERROR_UPLOAD_ERR_TYPE}
				<tr><td>&nbsp;</td><td><span class="errormsg">{t}Only the following podcast format is allowed: MP3.{/t}</span></td></tr>
			{/if}
		{/if}

		{if $action=='add'}
			<tr>
				<td class="help" colspan="2">
					<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" alt="{t}Note{/t}" title="{t}Note{/t}">
					{t}Only the following podcast format is allowed: MP3.{/t}<br />
					{t}The file size of the podcasts must not exceed 1500 KB. Recommended MP3 quality 22kHz MONO.{/t}<br />
					{t}After click to upload, it can take a while, until the next page is been shown.{/t}
				</td>
			</tr>
		{/if}

		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td class="header-small" colspan="2">
				<input type="reset" name="reset" value="{t}Reset{/t}" style="width:120px"/>&nbsp;&nbsp;
				<input type="submit" name="ok" value="{if $action=='add'}{t}Upload{/t}{else}{t}Submit{/t}{/if}" style="width:120px"/>
			</td>
		</tr>
	</table>
</form>