{***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************}
 {* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-winner.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Special caches{/t}" />
	{t}Special caches{/t}
</div>

<table class="table">
	<tr>
		<td style="padding-left:32px; padding-bottom:32px;">
			{t 1=$opt.template.style}The following list is generated automatically by the given recommendations of the users. You can find more informations on
			regional classification in the <a href="http://blog.geocaching.de/?page_id=271">help</a>.<br />
			<br />
			The numbers in the list below means:<br />
			<img src="images/rating-star.gif" border="0" alt="Recommendations"> Number of users that recommend this cache<br />
			<img src="resource2/%1/images/log/16x16-found.png" width="16" height="16" border="0" alt="Found"> Checks = Number of time the cache was found<br />
			Index tries to take the number of recommendations and founds in an order to show 'the best' geocache first.<br />
			<img src="images/tops-formula.png" border="0" alt="Formula">{/t}
		</td>
	</tr>
	<tr>
		<td style="padding-left:32px; padding-bottom:32px;">
			<table width="100%">
				{foreach name=adm1 from=$tops item=adm1item}
					<tr>
						<td valign="top" width="150px">{$adm1item.name|escape}</td>
						<td>
							{foreach name=adm3 from=$adm1item.adm3 item=adm3item}
								{if $adm3item.name==null}
									<a href="#{$adm1item.name|urlencode}null"><i>(ohne geogr. Bezug)</i><br /></a>
								{else}
									<a href="#{$adm1item.name|urlencode}{$adm3item.name|urlencode}">{$adm3item.name|escape}</a><br />
								{/if}
							{/foreach}
						</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>

{foreach name=adm1 from=$tops item=adm1item}
	{foreach name=adm3 from=$adm1item.adm3 item=adm3item}
			<p class="content-title-noshade-size3"><a name="{$adm1item.name|urlencode}{if $adm3item.name==null}null{else}{$adm3item.name|urlencode}{/if}"></a> {$adm1item.name|escape}
								 &gt; 

								{if $adm3item.name==null}
									(ohne geogr. Bezug)
								{else}
									{$adm3item.name|escape}
								{/if}</p>
			<table class="table">
				<tr>
					<td align="right"><b>{t}Index{/t}</b></td>
					<td align="center"><img src="images/rating-star.gif" border="0" alt="{t}Recommendations{/t}"></td>
					<td align="center"><img src="resource2/{$opt.template.style}/images/log/16x16-found.png" width="16" height="16" border="0" alt="{t}Found{/t}"></td>
					<td>&nbsp;</td>
				</tr>
				{foreach name=cache from=$adm3item.items item=cacheItem}
					<tr>
						<td width="40px" align="right">
							{$cacheItem.idx}
						</td>
						<td width="40px" align="center">
							{$cacheItem.ratings}
						</td>
						<td width="60px" align="center">
							{$cacheItem.founds}
						</td>
						<td>
							<a href="viewcache.php?wp={$cacheItem.wpoc}">{$cacheItem.name|escape}</a> {t}by{/t} <a href="viewprofile.php?userid={$cacheItem.userid}">{$cacheItem.username|escape}</a>
						</td>
					</tr>
				{/foreach}
			</table>
	{/foreach}
{/foreach}