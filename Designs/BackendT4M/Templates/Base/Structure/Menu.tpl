<script type="text/javascript">

$(function()
{
	$('.Menu > li a').click(function()
	{
		$(this).siblings('ul').show();
		$(this).parent().siblings().children('ul').hide();
	});
});

</script>
{?Items}
<ul class="Menu">
	{@Items}
		{@PreviousLevelOffset}</ul></li>{/@PreviousLevelOffset}
		<li class="Level{$OffsetLevel}{?Submenu} Submenu{/?Submenu}{?Selected} Selected{/?Selected}">
			<a href="javascript:" {?!Submenu}onclick="Load('.Body', '{$Url}')"{/?!Submenu}>{$Name}</a>
		
		{?!Submenu}</li>{/?!Submenu}
		{?Submenu}<ul class="Level{$OffsetLevel}">{/?Submenu}
		
		{?_Last}
		{@OffsetLevel}</ul></li>{/@OffsetLevel}
		{/?_Last}
	{/@Items}
</ul>
{/?Items}