<div class="Menu Sites">
	<ul>
		<li class="Level0 Submenu FromLeft">
			<a href="{$_SiteUrl}">{$_HostName}</a>
			<div class="Level0">
				<span class="Arrow"></span>
				<ul>
					{@Sites}
					<li class="Level1"><a href="{$Url}" target="_blank">{$Name}</a></li>
					{/@Sites}
				</ul>
			</div>
		</li>
	</ul>
</div>