{?BlockCategories}
	<div class="LeftColumn">
		<div class="Site">
			Site:<br />
			<select onchange="Load('.Body', '{$._ArticleUrl}?Site='+this.value)" style="width: 230px;">
				{@Sites}
				<option value="{$ID}"{?Selected} selected="selected"{/?Selected}>{$Name}</option>
				{/@Sites}
			</select>
		</div>
		{$Categories.HTML}
	</div>
	<div class="RightColumn">
		{?ActionLoad}
		<script type="text/javascript">Load('.RightColumn', '{$ActionLoad.HTML}')</script>
		{/?ActionLoad}
	</div>
{/?BlockCategories}



{?BlockArticles}
	<form class="ArticleList" action="{$ActionDelete}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.RightColumn'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.ArticleList').hide().siblings('.ArticleCreate').show()">Artikel erstellen</a>
			{?Articles}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Articles}
			<h1><a {?Browser}href="javascript:" onclick="Browser.Select('{$CategoryUrl}')"{/?Browser}{?!Browser}href="{$_HostUrl}{$CategoryUrl}" target="_blank"{/?!Browser}>{$CategoryName}</a> <span style="color: #cccccc;">&bull; ID {$CategoryID} &bull; {$Articles.Count} Artikel</span></h1>
		</div>
		{?Articles}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="80" />
					<col />
					<col />
					<col width="50" />
					<col width="100" />
					<col width="180" />
					<col width="120" />
					<col width="100" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Typ</th>
					<th>Layout</th>
					<th>Sortierschlüssel</th>
					<th>Status</th>
					<th>&nbsp;</th>
				</tr>
				{@Articles}
					<tr>
						<td><input name="DeleteArticle[]" type="checkbox" value="{$ID}" /></td>
						<td>{$ID}</td>
						<td>
							{?.Browser}
							<a href="javascript:" onclick="Browser.Select('{$Url}');">{$Name}</a>
							{/?.Browser}
							{?!.Browser}
							<a href="javascript:" onclick="Load('.RightColumn', '{$._ArticleUrl}?Article={$ID}')">{$Name}</a>
							{/?!.Browser}
						</td>
						<td>{$Key}</td>
						<td>{$ContentType}</td>
						<td>{$Layout}{?!Layout}<span style="color: #cccccc;">{$AutoLayout}</span>{/?!Layout}</td>
						<td>{$SortKey}</td>
						<td>
							{?Status=0}Inaktiv{/?Status=0}
							{?Status=1}Aktiv (unsichtbar){/?Status=1}
							{?Status=2}Aktiv (sichtbar){/?Status=2}
							{?Status=3}Startartikel{/?Status=3}
							{?Status=4}Abfangartikel{/?Status=4}
						</td>
						<td>
							<button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button>
							<a class="Button Right Web" href="{$Url}" target="_blank"></a>
						</td>
					</tr>
				{/@Articles}
			</table>
		{/?Articles}
		
		{?!Articles}
			<p>Die Kategorie ist leer.</p>
		{/?!Articles}
	</form>
	
	<form class="ArticleCreate" action="{$ActionSave}" onsubmit="SubmitForm(this, '.RightColumn'); return false;" style="display: none; padding-top: 130px;">
		<input type="hidden" name="Article[Category]" value="{$CategoryID}" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.ArticleCreate').hide().siblings().show()">Zurück</a>
			<h1>Neuen Artikel in "{$CategoryName}" erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="Article[Name]" type="text"{?!Articles} value="{$CategoryName}"{/?!Articles} />
			</li>
			<li>
				<label>Key</label>
				<input name="Article[Key]" type="text"{?!Articles} value="{$CategoryKey}"{/?!Articles} />
			</li>
			<li>
				<label>Typ</label>
				<select name="Article[ContentType]">
					<option value="html">html</option>
				</select>
			</li>
			<li>
				<label>Layout</label>
				<input name="Article[Layout]" type="text" />
			</li>
			<li>
				<label>Zugriffsrecht</label>
				<input name="Article[Permission]" type="text" />
			</li>
			<li>
				<label>Sortierschlüssel</label>
				<input name="Article[SortKey]" type="text" />
			</li>
			<li>
				<label>Status</label>
				<select name="Article[Status]">
					<option value="0">Inaktiv</option>
					<option value="1">Aktiv (unsichtbar)</option>
					<option value="2">Aktiv (sichtbar)</option>
					<option value="3"{?!Articles} selected="selected"{/?!Articles}>Startartikel</option>
					<option value="4">Abfangartikel</option>
				</select>
			</li>
		</ul>
	</form>
{/?BlockArticles}



{?BlockArticle}
	<form action="{$ActionSave}" onsubmit="SubmitForm(this, '.RightColumn'); return false;" style="padding-top: 130px;">
		<input name="Article[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="Load('.RightColumn', '{$._ArticleUrl}?Category={$Category}')">Zurück</a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}"><a href="{$_HostUrl}{$Url}" target="_blank">{$Name}</a> <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form Advanced">
			<li>
				<label>Einstellungen</label>
				<a href="javascript:" onclick="$(this).parent().siblings().toggle()">Öffnen / Schließen</a>
			</li>
			<li>
				<label>Name</label>
				<input name="Article[Name]" type="text" value="{$Name}" />
			</li>
			<li>
				<label>Key</label>
				<input name="Article[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Typ</label>
				<select name="Article[ContentType]">
					<option value="html"{?ContentType=html} selected="selected"{/?ContentType=html}>html</option>
				</select>
			</li>
			<li>
				<label>Kategorie</label>
				<select name="Article[Category]">
					{@Categories}
						<option value="{$ID}" class="Level{$Level}"{?Selected} selected="selected"{/?Selected}>{$Name}</option>
					{/@Categories}
				</select>
			</li>
			<li>
				<label>Layout</label>
				<input name="Article[Layout]" type="text" value="{$Layout}"{?Layout.empty} placeholder="{AutoLayout} {$AutoLayout}"{/?Layout.empty} />
				
			</li>
			<li>
				<label>Zugriffsrecht</label>
				<input name="Article[Permission]" type="text" value="{$Permission}"{?Permission.empty} placeholder="{AutoPermission} {$AutoPermission}"{/?Permission.empty} />
			</li>
			<li>
				<label>Sortierschlüssel</label>
				<input name="Article[SortKey]" type="text" value="{$SortKey}" />
			</li>
			<li>
				<label>Status</label>
				<select name="Article[Status]">
					<option value="0"{?Status=0} selected="selected"{/?Status=0}>Inaktiv</option>
					<option value="1"{?Status=1} selected="selected"{/?Status=1}>Aktiv (unsichtbar)</option>
					<option value="2"{?Status=2} selected="selected"{/?Status=2}>Aktiv (sichtbar)</option>
					<option value="3"{?Status=3} selected="selected"{/?Status=3}>Startartikel</option>
					<option value="4"{?Status=4} selected="selected"{/?Status=4}>Abfangartikel</option>
				</select>
			</li>
		</ul>
		{?Attributes}
		<ul class="Form">
			{@Attributes}
			<li>
				<label title="{$Key}">{$Name}{?!ID}*{/?!ID}</label>
				{?Type=Readonly}
				<input type="text" readonly value="{$Value}" style="width: 500px;" />
				{/?Type=Readonly}
				{?Type=Value}
				<input name="Article[Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
				{/?Type=Value}
				{?Type=Text}
				<textarea name="Article[Attributes][{$Key}]" style="width: 500px; height: 100px;">{$Value}</textarea>
				{/?Type=Text}
				{?Type=Html}
				<textarea class="Html" name="Article[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				<div class="Buttons">
					<a class="Button Left" href="javascript:" onclick="Editor.Open('{$.Name} &bull; {$Key}', $(this).parent().prev().get(0))">WYSIWYG-Editor</a>
				</div>
				{/?Type=Html}
			</li>
			{/@Attributes}
		</ul>
		{/?Attributes}
		
		<a class="Button Right" href="javascript:" onclick="$(this).siblings('.InstanceCreate').show().find('input, select, textarea').removeAttr('disabled')">Erstellen</a>
		<h2 style="margin: 30px 0 0 0;">Zusätzliche Module <span style="color: #cccccc;">&bull; {$Instances.Count}</span></h2>
		<div class="InstanceCreate" style="border-top: 1px solid #dddddd; padding: 15px 0 10px 0; display: none;">
			<h3>Neues Modul</h3>
			<div class="Buttons">
				<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.InstanceCreate').hide().find('input, select, textarea').attr('disabled', 'disabled')">Schließen</a>
			</div>
			<ul class="Form Create">
				<li>
					<label>Key</label>
					<input name="Instances[new][Key]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>Container</label>
					<input name="Instances[new][Container]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>View</label>
					<input name="Instances[new][View]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>Template</label>
					<input name="Instances[new][Template]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>Zugriffsrecht</label>
					<input name="Instances[new][Permission]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>Sortierschlüssel</label>
					<input name="Instances[new][SortKey]" type="text" disabled="disabled" />
				</li>
				<li>
					<label>Status</label>
					<select name="Instances[new][Status]" disabled="disabled">
						<option value="0">Inaktiv</option>
						<option value="1" selected="selected">Aktiv</option>
					</select>
				</li>
			</ul>
		</div>
		{@Instances}
			<div style="border-top: 1px solid #dddddd; padding: 15px 0 10px 0;">
				<a class="Button Right Delete" href="javascript:" onclick="Load('.RightColumn', '{$ActionRemove}')"></a>
				<h3 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}">{$Key} <span style="color: #cccccc;">&bull; ID {$ID}</span></h3>
				<ul class="Form Advanced">
					<li>
						<label>Einstellungen</label>
						<a href="javascript:" onclick="$(this).parent().siblings().toggle()">Öffnen / Schließen</a>
					</li>
					<li>
						<label>Key</label>
						<input name="Instances[{$ID}][Key]" type="text" value="{$Key}" />
					</li>
					<li>
						<label>Container</label>
						<input name="Instances[{$ID}][Container]" type="text" value="{$Container}" />
					</li>
					<li>
						<label>View</label>
						<input name="Instances[{$ID}][View]" type="text" value="{$View}" />
					</li>
					<li>
						<label>Template</label>
						<input name="Instances[{$ID}][Template]" type="text" value="{$Template}" />
					</li>
					<li>
						<label>Zugriffsrecht</label>
						<input name="Instances[{$ID}][Permission]" type="text" value="{$Permission}" />
					</li>
					<li>
						<label>Sortierschlüssel</label>
						<input name="Instances[{$ID}][SortKey]" type="text" value="{$SortKey}" />
					</li>
					<li>
						<label>Status</label>
						<select name="Instances[{$ID}][Status]">
							<option value="0"{?Status=0} selected="selected"{/?Status=0}>Inaktiv</option>
							<option value="1"{?Status=1} selected="selected"{/?Status=1}>Aktiv</option>
						</select>
					</li>
				</ul>
				{?Attributes}
				<ul class="Form">
					{@Attributes}
					<li>
						<label title="{$Key}">{$Name}{?!ID}*{/?!ID}</label>
						{?Type=Readonly}
						<input type="text" readonly value="{$Value}" style="width: 500px;" />
						{/?Type=Readonly}
						{?Type=Value}
						<input name="Instances[{$.ID}][Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
						{/?Type=Value}
						{?Type=Text}
						<textarea name="Instances[{$.ID}][Attributes][{$Key}]" style="width: 500px; height: 100px;">{$Value}</textarea>
						{/?Type=Text}
						{?Type=Html}
						<textarea class="Html" name="Instances[{$.ID}][Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
						<div class="Buttons">
							<a class="Button Left" href="javascript:" onclick="Editor.Open('{$..Name} &bull; {$.Key} &bull; {$Key}', $(this).parent().prev().get(0))">WYSIWYG-Editor</a>
						</div>
						{/?Type=Html}
					</li>
					{/@Attributes}
				</ul>
				{/?Attributes}
				
				{?Contents}
				<ul class="Form">
					{@Contents}
					<li>
						<label title="{$Key}">{$Name}{?!ID}*{/?!ID}</label>
						{?Type=Value}
						<input name="Instances[{$.ID}][Contents][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
						{/?Type=Value}
						{?Type=Text}
						<textarea name="Instances[{$.ID}][Contents][{$Key}]" style="width: 500px; height: 100px;">{$Value}</textarea>
						{/?Type=Text}
						{?Type=Html}
						<textarea class="Html" name="Instances[{$.ID}][Contents][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
						<div class="Buttons">
							<a class="Button Left" href="javascript:" onclick="Editor.Open('{$..Name} &bull; {$.Key} &bull; {$Key}', $(this).parent().prev().get(0))">WYSIWYG-Editor</a>
						</div>
						{/?Type=Html}
						{?!Type}
						UNKNOWN
						{/?!Type}
					</li>
					{/@Contents}
				</ul>
				{/?Contents}
			</div>
		{/@Instances}
	</form>
{/?BlockArticle}