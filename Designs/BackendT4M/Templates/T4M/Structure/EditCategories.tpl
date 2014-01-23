<div class="LeftColumn">
	<div class="Site">
		Site:<br />
		<select onchange="Load('.Body', '{$._ArticleUrl}?Site='+this.value)" style="width: 230px; margin: 0 0 10px 0;">
			{@Sites}
			<option value="{$ID}"{?Selected} selected="selected"{/?Selected}>{$Name}</option>
			{/@Sites}
		</select>
		{?ID}
		<a class="Button" href="javascript:" onclick="$('.RightColumn .EditCategory').hide().siblings('.CreateCategory').show()">Neue Kategorie erstellen</a>
		{/?ID}
	</div>
	{$Categories.HTML}
</div>
<div class="RightColumn">
	{?ID}
	<form class="EditCategory" action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<input name="Category[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Delete" href="javascript:" onclick="if ( confirm('Wirklich löschen?') ) Load('.Body', '{$ActionDelete}')" title="Löschen"></a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}"><a href="{$_HostUrl}{$Url}" target="_blank">{$Name}</a> <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="Category[Name]" type="text" value="{$Name}" />
			</li>
			{?!Status=3}
			<li>
				<label>Key</label>
				<input name="Category[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Kategorie</label>
				<select name="Category[Parent]">
					{@ParentCategories}
						<option value="{$ID}" class="Level{$Level}"{?SelectedParent} selected="selected"{/?SelectedParent}>{$Name}</option>
					{/@ParentCategories}
				</select>
			</li>
			{/?!Status=3}
			<li>
				<label>Layout</label>
				<input name="Category[Layout]" type="text" value="{$Layout}"{?Layout.empty} placeholder="{AutoLayout} {$AutoLayout}"{/?Layout.empty} />
			</li>
			<li>
				<label>Zugriffsrecht</label>
				<input name="Category[Permission]" type="text" value="{$Permission}"{?Permission.empty} placeholder="{AutoPermission} {$AutoPermission}"{/?Permission.empty} />
			</li>
			<li>
				<label>Sortierschlüssel</label>
				<input name="Category[SortKey]" type="text" value="{$SortKey}" />
			</li>
			{?!Status=3}
			<li>
				<label>Status</label>
				<select name="Category[Status]">
					<option value="0"{?Status=0} selected="selected"{/?Status=0}>Inaktiv</option>
					<option value="1"{?Status=1} selected="selected"{/?Status=1}>Aktiv (unsichtbar)</option>
					<option value="2"{?Status=2} selected="selected"{/?Status=2}>Aktiv (sichtbar)</option>
				</select>
			</li>
			{/?!Status=3}
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
				<input name="Category[Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
				{/?Type=Value}
				{?Type=Text}
				<textarea name="Category[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				{/?Type=Text}
				{?Type=Html}
				<textarea class="Html" name="Category[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				<div class="Buttons">
					<a class="Button Left" href="javascript:" onclick="Editor.Open('{$.Name} &bull; {$Key}', $(this).parent().prev().get(0))">WYSIWYG-Editor</a>
				</div>
				{/?Type=Html}
			</li>
			{/@Attributes}
		</ul>
		{/?Attributes}
	</form>
	{/?ID}
	<form class="CreateCategory" action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;{?ID} display: none;{/?ID}">
		{?!ID}
		<input name="Category[Parent]" type="hidden" value="{$SiteID}" />
		<input name="Category[Status]" type="hidden" value="3" />
		{/?!ID}
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			{?ID}
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.CreateCategory').hide().siblings('.EditCategory').show()">Zurück</a>
			{/?ID}
			<h1>{?ID}Neue Kategorie{/?ID}{?!ID}Hauptkategorie{/?!ID} erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="Category[Name]" type="text" />
			</li>
			{?ID}
			<li>
				<label>Key</label>
				<input name="Category[Key]" type="text" />
			</li>
			
			<li>
				<label>Kategorie</label>
				<select name="Category[Parent]">
					{@CreateCategories}
						<option value="{$ID}" class="Level{$Level}"{?Selected} selected="selected"{/?Selected}>{$Name}</option>
					{/@CreateCategories}
				</select>
			</li>
			{/?ID}
			<li>
				<label>Layout</label>
				<input name="Category[Layout]" type="text" />
			</li>
			<li>
				<label>Zugriffsrecht</label>
				<input name="Category[Permission]" type="text" />
			</li>
			<li>
				<label>Sortierschlüssel</label>
				<input name="Category[SortKey]" type="text" />
			</li>
			{?ID}
			<li>
				<label>Status</label>
				<select name="Category[Status]">
					<option value="0">Inaktiv</option>
					<option value="1">Aktiv (unsichtbar)</option>
					<option value="2">Aktiv (sichtbar)</option>
				</select>
			</li>
			{/?ID}
		</ul>
	</form>
</div>