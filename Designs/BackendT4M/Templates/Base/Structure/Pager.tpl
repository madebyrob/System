<div class="Pager">
	<div class="Total">
		<strong>{$Total}</strong> Einträge
	</div>
	<div class="Pages">
		{?PreviousPage}<a class="Button Left" href="javascript:" onclick="Load('.Body', '{$_ArticleUrl}?Limit={$Limit}&Page={$PreviousPage}')"><span>◄</span></a>{/?PreviousPage}
		{?NextPage}<a class="Button Right" href="javascript:" onclick="Load('.Body', '{$_ArticleUrl}?Limit={$Limit}&Page={$NextPage}')"><span>►</span></a>{/?NextPage}
		{?Pages>1}Seite <input style="width: 30px; text-align: center;" class="Input" onkeyup="if ( event.keyCode == 13 ) Load('.Body', '{$_ArticleUrl}?Limit={$Limit}&Page='+this.value)" value="{$Page}" /> von <strong>{$TotalPages}</strong>{/?Pages>1}
	</div>
	<div class="Limit">
		<select class="Input" onchange="Load('.Body', '{$_ArticleUrl}?Limit='+this.value)">
			<option style="padding: 0 5px"{?Limit=10} selected="selected"{/?Limit=10} value="10">10</option>
			<option style="padding: 0 5px"{?Limit=50} selected="selected"{/?Limit=50} value="50">50</option>
			<option style="padding: 0 5px"{?Limit=100} selected="selected"{/?Limit=100} value="100">100</option>
		</select>
		pro Seite
	</div>
</div>