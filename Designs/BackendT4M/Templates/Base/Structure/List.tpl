{?Items}
<div class="List {$_InstanceKey}">
	<ul>
		{@Items}
			{@PreviousLevelOffset}</ul></div></li>{/@PreviousLevelOffset}
			<li class="Item Level{$OffsetLevel}{?Submenu} Submenu{/?Submenu}{?Selected} Selected{/?Selected}{?Last} Last{/?Last}{?First} First{/?First}">
				<a href="{$Url}">{$Name}</a>
			
			{?!Submenu}</li>{/?!Submenu}
			{?Submenu}<div class="Level{$OffsetLevel}"><ul>{/?Submenu}
			
			{?_Last}
			{@OffsetLevel}</ul></div></li>{/@OffsetLevel}
			{/?_Last}
		{/@Items}
	</ul>
</div>
{/?Items}