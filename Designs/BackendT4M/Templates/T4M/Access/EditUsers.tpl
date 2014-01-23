<div class="LeftColumn">
</div>

<div class="RightColumn">
{?BlockMain}
	<form class="UserList" action="{$ActionDelete}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.UserList').hide().siblings('.UserCreate').show()">Benutzer erstellen</a>
			{?Users}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Users}
			<h1>Benutzer <span style="color: #cccccc;">&bull; {$Users.Count}</span></h1>
		</div>
		{?Users}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="80" />
					<col />
					<col />
					<col width="200" />
					<col width="200" />
					<col width="120" />
					<col width="100" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>E-Mail-Adresse</th>
					<th>Gruppen</th>
					<th>Status</th>
					<th>&nbsp;</th>
				</tr>
				{@Users}
					<tr>
						<td><input name="DeleteUser[]" type="checkbox" value="{$ID}" /></td>
						<td>{$ID}</td>
						<td><a href="javascript:" onclick="Load('.Body', '{$ActionOpen}')">{$Name}</a></td>
						<td>{$Key}</td>
						<td>{$EmailAddress}</td>
						<td>{$Groups}</td>
						<td>
							{?Status=0}Inaktiv{/?Status=0}
							{?Status=1}Aktiv{/?Status=1}
							{?Status=2}Angemeldet{/?Status=2}
						</td>
						<td>
							<button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button>
						</td>
					</tr>
				{/@Users}
			</table>
		{/?Users}
		
		{?!Users}
			<p>Keine Benutzer vorhanden.</p>
		{/?!Users}
	</form>
	
	<form class="UserCreate" action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="display: none; padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.UserCreate').hide().siblings().show()">Zurück</a>
			<h1>Neuen Benutzer erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="User[Name]" type="text" />
			</li>
			<li>
				<label>Key</label>
				<input name="User[Key]" type="text" />
			</li>
			<li>
				<label>Passwort</label>
				<input name="User[Password]" type="text" />

			</li>
			<li>
				<label>E-Mail-Adresse</label>
				<input name="User[EmailAddress]" type="text" />
			</li>
			<li>
				<label>Gruppen</label>
				<input name="User[Groups]" type="text" />
			</li>
			<li>
				<label>Status</label>
				<select name="User[Status]">
					<option value="0">Inaktiv</option>
					<option value="1">Aktiv</option>
				</select>
			</li>
		</ul>
	</form>
{/?BlockMain}

{?BlockEdit}
	<form action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<input name="User[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Delete" href="javascript:" onclick="if ( confirm('Wirklich löschen?') ) Load('.Body', '{$ActionDelete}')" title="Löschen"></a>
			<a class="Button Right Light" href="javascript:" onclick="Load('.Body', '{$._ArticleUrl}')">Zurück</a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}">{$Name} <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form">
			<li>
				<label>Name</label>
				<input name="User[Name]" type="text" value="{$Name}" />
			</li>
			<li>
				<label>Key</label>
				<input name="User[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Password</label>
				<input name="User[Password]" type="text" />
			</li>
			<li>
				<label>E-Mail-Adresse</label>
				<input name="User[EmailAddress]" type="text" value="{$EmailAddress}" />
			</li>
			<li>
				<label>Gruppen</label>
				<input name="User[Groups]" type="text" value="{$Groups}" />
			</li>
			<li>
				<label>Status</label>
				<select name="User[Status]">
					<option value="0"{?Status=0} selected="selected"{/?Status=0}>Inaktiv</option>
					<option value="1"{?Status=1} selected="selected"{/?Status=1}>Aktiv / Abgemeldet</option>
					<option value="2"{?Status=2} selected="selected"{/?Status=2}>Aktiv / Angemeldet</option>
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
				<input name="User[Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
				{/?Type=Value}
				{?Type=Text}
				<textarea name="User[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				{/?Type=Text}
				{?Type=Html}
				<textarea class="Html" name="User[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
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