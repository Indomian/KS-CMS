{literal}<?xml version="1.0" encoding="utf-8"?>{/literal}
<rss version="2.0">
	<channel>
		<title>{$module_title} "{$SITE.home_title}"</title>
		<link>{$SITE.home_url}</link>
		<description>{$SITE.home_descr}</description>
		<language>ru</language>
		<pubDate>{$data.pubDate}</pubDate>
		<lastBuildDate>{$data.lastBuildDate}</lastBuildDate>
		{foreach from=$announces key=record_key item=record}
		<item>
			<title>{$record.title}</title>
			<description>{$record.content}</description>
			<link>{$SITE.home_url}{$record.section.path}{$record.text_ident}.html</link>
			<guid isPermaLink="true">{$SITE.home_url}{$record.section.path}{$record.text_ident}.html</guid>
			<pubDate>{$record.date_rfc2822}</pubDate>
		</item>
		{/foreach}
	</channel>
</rss>