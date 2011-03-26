<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/description/32x32-rss.png" style="align: left; margin-right: 10px;" alt="{t}Subscribe to feeds from opencaching{/t}" />
	{t}Subscribe to feeds from opencaching{/t}
</div>

<table width="100%" class="table">
	{foreach from=$feeds item=feed}
		<tr><td>
			<img src="resource2/{$opt.template.style}/images/description/16x16-rss.png" class="icon16" alt="" title="" align="middle" />&nbsp;<a class="links" href="{$feed.link}">{$feed.title}</a>
		</td></tr>
	{/foreach}
</table>