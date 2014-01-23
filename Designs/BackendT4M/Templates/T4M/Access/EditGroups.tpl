<div class="LeftColumn">
</div>

<div class="RightColumn">
{?BlockMain}
	<form class="GroupList" action="{$ActionDelete}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.GroupList').hide().siblings('.GroupCreate').show()">Gruppe erstellen</a>
			{?Groups}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Groups}
			<h1>Gruppen <span style="color: #cccccc;">&bull; {$Groups.Count}</span></h1>
		</div>
		{?Groups}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="80" />
					<col />
					<col />
					<col width="120" />
					<col width="100" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Status</th>
					<th>&nbsp;</th>
				</tr>
				{@Groups}
					<tr>
						<td><input name="DeleteGroup[]" type="checkbox" value="{$ID}" /></td>
						<td>{$ID}</td>
						<td><a href="javascript:" onclick="Load('.Body', '{$ActionOpen}')">{$Name}</a></td>
						<td>{$Key}</td>
						<td>
							{?Status=0}Inaktiv{/?Status=0}
							{?Status=1}Aktiv{/?Status=1}
						</td>
						<td>
							<button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button>
						</td>
					</tr>
				{/@Groups}
			</table>
		{/?Groups}
		
		{?!Groups}
			<p>Keine Benutzer vorhanden.</p>
		{/?!Groups}
	</form>
	
	<form class="GroupCreate" action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="display: none; padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.GroupCreate').hide().siblings().show()">Zurück</a>
			<h1>Neue Gruppe erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Key</label>
				<input name="Group[Key]" type="text" />
			</li>
			<li>
				<label>Status</label>
				<select name="Group[Status]">
					<option value="0">Inaktiv</option>
					<option value="1">Aktiv</option>
				</select>
			</li>
		</ul>
	</form>
{/?BlockMain}

{?BlockEdit}
	<form action="{$ActionSave}" onsubmit="SubmitForm(this, '.Body'); return false;" style="padding-top: 130px;">
		<input name="Group[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Delete" href="javascript:" onclick="Load('.Body', '{$ActionDelete}')" title="Löschen"></a>
			<a class="Button Right Light" href="javascript:" onclick="Load('.Body', '{$._ArticleUrl}')">Zurück</a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}">{$Name} <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form">
			<li>
				<label>Key</label>
				<input name="Group[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Status</label>
				<select name="Group[Status]">
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
				<input name="Group[Attributes][{$Key}]" type="text" value="{$Value}" style="width: 500px;" />
				{/?Type=Value}
				{?Type=Text}
				<textarea name="Group[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
				{/?Type=Text}
				{?Type=Html}
				<textarea class="Html" name="Group[Attributes][{$Key}]" style="width: 500px; height: 200px; margin: 0 0 5px 0;">{$Value}</textarea>
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