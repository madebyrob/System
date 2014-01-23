{?BlockModels}
	<div class="LeftColumn">
		<div class="Menu">
			<ul>
				{@Models}
					<li class="{?Selected} Selected{/?Selected}">
						<a href="javascript:" onclick="Load('.RightColumn', '{$ActionOpen}'); $(this).parentsUntil('.Menu').find('.Selected').removeClass('Selected'); $(this).parent().addClass('Selected');">{$Name}</a>
					</li>
				{/@Models}
			</ul>
		</div>
	</div>
	<div class="RightColumn">
		{?ActionLoad}
		<script type="text/javascript">Load('.RightColumn', '{$ActionLoad.HTML}')</script>
		{/?ActionLoad}
	</div>
{/?BlockModels}

{?BlockMain}
	<form class="AttributeList" action="{$ActionDelete}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.RightColumn'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.AttributeList').hide().siblings('.AttributeCreate').show()">Attribut erstellen</a>
			{?Attributes}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Attributes}
			<h1>Attribute <span style="color: #cccccc;">&bull; {$Attributes.Count}</span></h1>
		</div>
		{?Attributes}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="80" />
					<col />
					<col />
					<col width="80" />
					<col width="40" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>ID</th>
					<th>Name</th>
					<th>Key</th>
					<th>Typ</th>
					<th>&nbsp;</th>
				</tr>
				{@Attributes}
					<tr>
						<td><input name="DeleteAttribute[]" type="checkbox" value="{$ID}" /></td>
						<td>{$ID}</td>
						<td><a href="javascript:" onclick="Load('.RightColumn', '{$ActionOpen}')">{$Name}</a></td>
						<td>{$Key}</td>
						<td>{$Type}</td>
						<td><button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button></td>
					</tr>
				{/@Attributes}
			</table>
		{/?Attributes}
		
		{?!Attributes}
			<p>Keine Attribute vorhanden.</p>
		{/?!Attributes}
	</form>
	
	<form class="AttributeCreate" action="{$ActionSave}" onsubmit="SubmitForm(this, '.RightColumn'); return false;" style="display: none; padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.AttributeCreate').hide().siblings().show()">Zurück</a>
			<h1>Neue Attribute erstellen</h1>
		</div>
		<ul class="Form">
			<li>
				<label>Key</label>
				<input name="Attribute[Key]" type="text" />
			</li>
			<li>
				<label>Type</label>
				<input name="Attribute[Type]" type="text" />
			</li>
			<li>
				<label>Model</label>
				<input name="Attribute[Model]" type="text" value="{$Model}" />
			</li>
		</ul>
	</form>
{/?BlockMain}

{?BlockEdit}
	<form action="{$ActionSave}" onsubmit="SubmitForm(this, '.RightColumn'); return false;" style="padding-top: 130px;">
		<input name="Attribute[ID]" value="{$ID}" type="hidden" />
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('form').submit()">Speichern</a>
			<a class="Button Right Delete" href="javascript:" onclick="Load('.RightColumn', '{$ActionDelete}&Model={$Model}')" title="Löschen"></a>
			<a class="Button Right Light" href="javascript:" onclick="Load('.RightColumn', '{$._ArticleUrl}?Model={$Model}')">Zurück</a>
			<h1 title="Erstellt am {$CreateTime.DateTimeShort}{?CreateUser} von {$CreateUser}{/?CreateUser}{?UpdateTime}, aktualisiert am {$UpdateTime.DateTimeShort}{?UpdateUser} von {$UpdateUser}{/?UpdateUser}{/?UpdateTime}">{$Name} <span style="color: #cccccc;">&bull; ID {$ID}</span></h1>
		</div>
		<ul class="Form">
			<li>
				<label>Key</label>
				<input name="Attribute[Key]" type="text" value="{$Key}" />
			</li>
			<li>
				<label>Type</label>
				<input name="Attribute[Type]" type="text" value="{$Type}" />
			</li>
			<li>
				<label>Model</label>
				<input name="Attribute[Model]" type="text" value="{$Model}" />
			</li>
		</ul>
	</form>
{/?BlockEdit}