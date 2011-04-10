{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
<script type="text/javascript">
<!--
{literal}
function insertSmiley(parSmiley) {
  var myText = document.logform.logtext;
  myText.focus();
  /* fuer IE */
  if(typeof document.selection != 'undefined') {
    var range = document.selection.createRange();
    var selText = range.text;
    range.text = parSmiley + selText;
  }
  /* fuer Firefox/Mozilla-Browser */
  else if(typeof myText.selectionStart != 'undefined')
  {
    var start = myText.selectionStart;
    var end = myText.selectionEnd;
    var selText = myText.value.substring(start, end);
    myText.value = myText.value.substr(0, start) + parSmiley + selText + myText.value.substr(end);
    /* Cursorposition hinter Smiley setzen */
    myText.selectionStart = start + parSmiley.length;
    myText.selectionEnd = start + parSmiley.length;
  }
  /* fuer die anderen Browser */
  else
  {
    alert(navigator.appName + ': {t escape=js}Setting smilies is not supported{/t}');
  }
}

function _chkFound () {
  if (document.logform.logtype.value == "1" || document.logform.logtype.value == "7")
	{
		if (document.logform.addRecommendation)
	    document.logform.addRecommendation.disabled = false;
  }
  else
  {
		if (document.logform.addRecommendation)
	    document.logform.addRecommendation.disabled = true;
  }
  return false;
}

{/literal}
//-->
</script>

<div class="content2-pagetitle">
	<img src="lang/de/ocstyle/images/description/22x22-logs.png" style="align: left; margin-right: 10px;" width="22" height="22" alt="" />
	{t 1=$cache.cacheid|escape 2=$cache.cachename|escape}Add log-entry for the cache <a href="viewcache.php?cacheid=%1">%2</a>{/t}
</div>

<form action="log2.php" method="post" enctype="application/x-www-form-urlencoded" name="logform" dir="ltr">
	<input type="hidden" name="cacheid" value="{$cache.cacheid|escape}"/>
	<input type="hidden" name="version3" value="1"/>
	<input id="descMode" type="hidden" name="descMode" value="1" />

	<table class="table">
		<tr>
			<td width="180px">{t}Type of log-entry:{/t}</td>
			<td>
				
					<select name="logtype" onChange="return _chkFound()">
						<option value="0"{if $logType==0} selected="selected"{/if}>{t}=== Please select ==={/t}</option>
						{foreach from=$logtypes item=logTypesItem}
							<option value="{$logTypesItem.id|escape}"{if $logType==$logTypesItem.id} selected="selected"{/if}>{$logTypesItem.name|escape}</value>
						{/foreach}
					</select>
					{if $noLogTypeSelected}
						<span class="errormsg">{t}Log type must be selected{/t}</span>
					{/if}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td width="180px">{t}Date:{/t}</td>
			<td>
				<input class="input40" type="text" name="logyear" maxlength="4" value="{$logDateYear|sprintf:'%04d'}"/>&nbsp;-
				<input class="input20" type="text" name="logmonth" maxlength="2" value="{$logDateMonth|sprintf:'%02d'}"/>&nbsp;-
				<input class="input20" type="text" name="logday" maxlength="2" value="{$logDateDay|sprintf:'%02d'}"/>
				{if $dateFormatInvalid==true}
					<span class="errormsg">{t}date is invalid, format: TT-MM-JJJJ{/t}</span>
				{/if}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td valign="top">{t}Recommendations:{/t}</td>
			<td valign="top">
				{if $userRecommended==true}
					<table class="table">
						<tr>
							<td><img src="resource2/{$opt.template.style}/images/viewcache/rating-star.gif" class="icon16" alt="" /></td>
							<td>{t}You have recommended this cache.{/t}</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="checkbox" name="revokeRecommendation" value="1" class="checkbox" />&nbsp;{t}Revoke the recommendation{/t}</td>
						</tr>
					</table>
				{elseif $userRecommendationUsed<$userRecommendationPossible}
					{* the user can rate this cache *}
					<input type="checkbox" name="addRecommendation" value="1" class="checkbox" />&nbsp;{t}This cache is one of my recommendations.{/t}<br />
					{t 1=$userRecommendationUsed 2=$userRecommendationPossible}You have given %1 of %2 possible recommendations.{/t}
				{else}
					{* the user cannot rate this cache *}
					{t 1=$userRecommendationRequiredFinds}You need additional %1 finds, to make another recommandation.{/t}<br />
					{t}Alternatively, you can withdraw a <a href="mytop5.php">existing recommendation</a>.{/t}
				{/if}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
	</table>

	<table class="table">
		<tr>
			<td colspan="2">{t}Log-entry:{/t}</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="menuBar">
					<span id="descText" class="buttonNormal" onclick="btnSelect(1)" onmouseover="btnMouseOver(1)" onmouseout="btnMouseOut(1)">{t}Text{/t}</span>
					<span class="buttonSplitter">|</span>
					<span id="descHtml" class="buttonNormal" onclick="btnSelect(2)" onmouseover="btnMouseOver(2)" onmouseout="btnMouseOut(2)">{t}&lt;html&gt;{/t}</span>
					<span class="buttonSplitter">|</span>
					<span id="descHtmlEdit" class="buttonNormal" onclick="btnSelect(3)" onmouseover="btnMouseOver(3)" onmouseout="btnMouseOut(3)">{t}Editor{/t}</span>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<span id="scriptwarning" class="errormsg">{t}JavaScript is disabled in your browser, you can enter text only. To use HTML, or the editor, please enable JavaScript.{/t}</span>
			</td>
		</tr>
		<tr>
			<td>
				<textarea name="logtext" id="logtext" cols="68" rows="25" >{$logText}</textarea>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				{foreach from=$smileys item=smileysItem}
					{if $smileysItem[0]==true}
						<a href="javascript:insertSmiley('{$smileysItem[1]}')">{$smileysItem[2]}</a>
					{/if}
				{/foreach}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		{if $requireLogPW==true}
			<tr>
				<td colspan="2">
					{t}Passwort to log:{/t}
					<input class="input100" type="text" name="log_pw" maxlength="20" value="" /> 
					{t}(only for found-logs){/t}
					{if $logPWValid==false}
						<span class="errormsg">{t}Invalid password!{/t}</span>
					{/if}
				</td>
			</tr>
			<tr><td class="spacer" colspan="2"></td></tr>
		{/if}
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td class="header-small" colspan="2">
				<input type="reset" name="reset" value="{t}Reset{/t}" style="width:120px"/>&nbsp;&nbsp;
				<input type="submit" name="submitform" value="{t}Log this cache{/t}" style="width:120px"/>
			</td>
		</tr>
	</table>
</form>

<script language="javascript" type="text/javascript">
<!--
{literal}
	/*
		1 = Text
		2 = HTML
		3 = HTML-Editor
	*/
	var use_tinymce = 0;
	var descMode = {/literal}{$descMode}{literal};
	document.getElementById("scriptwarning").firstChild.nodeValue = "";

	// set descMode to 1 or 2 ... when editor is loaded set to 3
	if (descMode == 3)
	{
		if (document.getElementById("logtext").value == '')
			descMode = 1;
		else
			descMode = 2;
	}

	document.getElementById("descMode").value = descMode;
	mnuSetElementsNormal();

	function postInit()
	{
		descMode = 3;
		use_tinymce = 1;
		document.getElementById("descMode").value = descMode;
		mnuSetElementsNormal();
	}

	function SwitchToTextDesc()
	{
		document.getElementById("descMode").value = 1;

		if (use_tinymce == 1)
			document.logform.submit();
	}

	function SwitchToHtmlDesc()
	{
		document.getElementById("descMode").value = 2;

		if (use_tinymce == 1)
			document.logform.submit();
	}

	function SwitchToHtmlEditDesc()
	{
		document.getElementById("descMode").value = 3;

		if (use_tinymce == 0)
			document.logform.submit();
	}

	function mnuSelectElement(e)
	{
		e.backgroundColor = '#D4D5D8';
		e.borderColor = '#6779AA';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuNormalElement(e)
	{
		e.backgroundColor = '#F0F0EE';
		e.borderColor = '#F0F0EE';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuHoverElement(e)
	{
		e.backgroundColor = '#B6BDD2';
		e.borderColor = '#0A246A';
		e.borderWidth = '1px';
		e.borderStyle = 'solid';
	}

	function mnuUnhoverElement(e)
	{
		mnuSetElementsNormal();
	}

	function mnuSetElementsNormal()
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (descMode)
		{
			case 1:
				mnuSelectElement(descText);
				mnuNormalElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 2:
				mnuNormalElement(descText);
				mnuSelectElement(descHtml);
				mnuNormalElement(descHtmlEdit);

				break;
			case 3:
				mnuNormalElement(descText);
				mnuNormalElement(descHtml);
				mnuSelectElement(descHtmlEdit);

				break;
		}
	}

	function btnSelect(mode)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		var oldMode = descMode;
		descMode = mode;
		mnuSetElementsNormal();

		if ((oldMode == 1) && (descMode != 1))
		{
			// convert text to HTML
			var desc = document.getElementById("logtext").value;

			if ((desc.indexOf('&amp;') == -1) &&
			    (desc.indexOf('&quot;') == -1) &&
			    (desc.indexOf('&lt;') == -1) &&
			    (desc.indexOf('&gt;') == -1) &&
			    (desc.indexOf('<p>') == -1) &&
			    (desc.indexOf('<i>') == -1) &&
			    (desc.indexOf('<strong>') == -1) &&
			    (desc.indexOf('<br />') == -1))
			{
				desc = desc.replace(/&/g, "&amp;");
				desc = desc.replace(/"/g, "&quot;");
				desc = desc.replace(/</g, "&lt;");
				desc = desc.replace(/>/g, "&gt;");
				desc = desc.replace(/\r\n/g, "\<br />");
				desc = desc.replace(/\n/g, "<br />");
				desc = desc.replace(/<br \/>/g, "<br />\n");
			}

			document.getElementById("logtext").value = desc;
		}

		switch (mode)
		{
			case 1:
				SwitchToTextDesc();
				break;
			case 2:
				SwitchToHtmlDesc();
				break;
			case 3:
				SwitchToHtmlEditDesc();
				break;
		}
	}

	function btnMouseOver(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuHoverElement(descText);
				break;
			case 2:
				mnuHoverElement(descHtml);
				break;
			case 3:
				mnuHoverElement(descHtmlEdit);
				break;
		}
	}

	function btnMouseOut(id)
	{
		var descText = document.getElementById("descText").style;
		var descHtml = document.getElementById("descHtml").style;
		var descHtmlEdit = document.getElementById("descHtmlEdit").style;

		switch (id)
		{
			case 1:
				mnuUnhoverElement(descText);
				break;
			case 2:
				mnuUnhoverElement(descHtml);
				break;
			case 3:
				mnuUnhoverElement(descHtmlEdit);
				break;
		}
	}
{/literal}
//-->
</script>