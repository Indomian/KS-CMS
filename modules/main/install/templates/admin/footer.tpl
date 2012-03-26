					</div>
				</td>
			</tr>
		</table>
		<div class="footer">
			<div class="footer_shell">
				<div class="copy">
					{$VERSION.COPYRIGHT}
				</div>
				<div class="version">
					{#system_version#} {$VERSION.ID} build {$VERSION.BUILD}
				</div>
				<ul class="footer_menu">
					<li>
						<a href="/admin.php?module=main&modpage=contribution">{#contribution#}</a>
					</li>
					<li>
						{if $showHelp=='Y'}
							<a href="/admin.php?module=help&{$help_url}">{#help#}</a>
						{else}
							<a href="mailto:{$helpEmail}">{#help#}</a>
						{/if}
					</li>
					<li><a href="http://www.kolosstudio.ru/" target="_blank">{#developers#}</a></li>
				</ul>
			</div></div>
		</div>
	</body>
</html>
