{?Items}
<div class="Menu">
	<ul>
		{@Items}
			{@PreviousLevelOffset}</ul></div></li>{/@PreviousLevelOffset}
			<li class="Level{$OffsetLevel}{?Submenu} Submenu{/?Submenu}{?Selected} Selected{/?Selected}">
				<a href="javascript:" onclick="Load('.Body', '{$ActionOpen}'); $(this).parentsUntil('.Menu').find('.Selected').removeClass('Selected'); $(this).parent().addClass('Selected');">{$Name}</a>
			
			{?!Submenu}</li>{/?!Submenu}
			{?Submenu}<div class="Level{$OffsetLevel}"><ul>{/?Submenu}
			
			{?_Last}
			{@OffsetLevel}</ul></div></li>{/@OffsetLevel}
			{/?_Last}
		{/@Items}
	</ul>
</div>
{/?Items}