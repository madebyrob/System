<div class="LeftColumn">
</div>

<div class="RightColumn">
{?BlockMain}
	<form class="SiteList" action="{$ActionDelete}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.SiteList').hide().siblings('.SiteCreate').show()">Site erstellen</a>
			{?Sites}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Sites}
			<h1>Sites <span style="color: #cccccc;">&bull; {$Sites.Count}</span></h1>
		</div>
		{?Sites}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="80" />
					<col />
					<col />
					<col width="100" />
					<col width="120" />
					<col width="100" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Lokalisierung</th>
					<th>Status</th>
					<th>&nbsp;</th>
				</tr>
				{@Sites}
					<tr>
						<td><input name="DeleteSite[]" type="checkbox" value="{$ID}" /></td>
						<td>{$ID}</td>
						<td><a href="javascript:" onclick="Load('.Body', '{$ActionOpen}')">{$Name}</a></td>
						<td>{$Key}</td>
						<td>{$Localization}</td>
						<td>
							{?Status=0}Inaktiv{/?Status=0}
							{?Status=1}Aktiv (unsichtbar){/?Status=1}
							{?Status=2}Aktiv (sichtbar){/?Status=2}
							{?Status=3}Standard{/?Status=3}
							{?Status=4}Backend{/?Status=4}
						</td>
						<td>
							<button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button>
						</td>
					</tr>
				{/@Sites}
			</table>
		{/?Sites}
		
		{?!Sites}
			<p>Keine Benutzer vorhanden.</p>
		{/?!Sites}
	</form>
	
	<form class="SiteCreate" action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="display: none; padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.SiteCreate').hide().siblings().show()">Zurück</a>
			<h1>Neue Site erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="Site[Name]" type="text" />
			</li>
			<li>
				<label>Key</label>
				<input name="Site[Key]" type="text" />
			</li>
			<li>
				<label>Lokalisierung</label>
				<input name="Site[Localization]" type="text" />

			</li>
			<li>
				<label>Status</label>
				<select name="Site[Status]">
					<option value="0">Inaktiv</option>
					<option value="1">Aktiv (unsichtbar)</option>
					<option value="2">Aktiv (sichtbar)</option>
					<option value="3">Standard</option>
					<option value="4">Backend</option>
				</select>
			</li>
		</ul>
	</form>
{/?BlockMain}

{?BlockEdit}
	<form action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<input name="Site[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Delete" href="javascript:" onclick="Load('.Body', '{$ActionDelete}')" title="Löschen"></a>
			<a class="Button Right Light" href="javascript:" onclick="Load('.Body', '{$._ArticleUrl}')">Zurück</a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}"><a href="{$_HostUrl}{$Url}" target="_blank">{$Name}</a> <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="Site[Name]" type="text" value="{$Name}" />
			</li>
			<li>
				<label>Key</label>
				<input name="Site[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Lokalisierung</label>
				<input name="Site[Localization]" type="text" value="{$Localization}" />
			</li>
			<li>
				<label>Status</label>
				<select name="Site[Status]">
					<option value="0"{?Status=0} selected="selected"{/?Status=0}>Inaktiv</option>
					<option value="1"{?Status=1} selected="selected"{/?Status=1}>Aktiv (unsichtbar)</option>
					<option value="2"{?Status=2} selected="selected"{/?Status=2}>Aktiv (sichtbar)</option>
					<option value="3"{?Status=3} selected="selected"{/?Status=3}>Standart</option>
					<option value="4"{?Status=4} selected="selected"{/?Status=4}>Backend</option>
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
				<input name="Site[Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
				{/?Type=Value}
				{?Type=Text}
				<textarea name="Site[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				{/?Type=Text}
				{?Type=Html}
				<textarea class="Html" name="Site[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				<div class="Buttons">
					<a class="Button Left" href="javascript:" onclick="Editor.Open('{$.Name} &bull; {$Key}', $(this).parent().prev().get(0))">WYSIWYG-Editor</a>
				</div>
				{/?Type=Html}
			</li>
			{/@Attributes}
		</ul>
		{/?Attributes}
	</form>
{/?BlockEdit}
</div>