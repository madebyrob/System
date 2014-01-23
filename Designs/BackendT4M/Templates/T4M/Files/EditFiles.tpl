{?BlockMain}
	<div class="LeftColumn">
		<div class="Site">
			<form action="{$ActionCreateDirectory}" onsubmit="SubmitForm(this, '.Columns'); return false;">
				<input class="CreateDirectoryPath" name="CreateDirectory[Path]" value="{$SelectedDirectory}" type="hidden" />
				Neues Unterverzeichnis:<br />
				<button class="Button Right Accept" type="submit"></button><input name="CreateDirectory[Name]" type="text" style="width: 185px;" />
			</form>
		</div>
		{?Directories}
		<div class="Menu">
			<ul>
				{@Directories}
					{@PreviousLevelOffset}</ul></div></li>{/@PreviousLevelOffset}
					<li class="Level{$Level}{?Submenu} Submenu{/?Submenu}{?Selected} Selected{/?Selected}">
						<a href="javascript:" onclick="$(this).parents('.LeftColumn').find('.CreateDirectoryPath').val('{$Key}'); Load('.RightColumn', '{$ActionOpen}'); $(this).parents('.Menu').find('.Selected').removeClass('Selected'); $(this).parent().addClass('Selected');">{$Name}</a>
					
					{?!Submenu}</li>{/?!Submenu}
					{?Submenu}<div class="Level{$Level}"><ul>{/?Submenu}
					
					{?_Last}
					{@Level}</ul></div></li>{/@Level}
					{/?_Last}
				{/@Directories}
			</ul>
		</div>
		{/?Directories}
	</div>
	<div class="RightColumn">
		{?ActionLoad}
		<script type="text/javascript">Load('.RightColumn', '{$ActionLoad.HTML}')</script>
		{/?ActionLoad}
	</div>
{/?BlockMain}



{?BlockDirectory}
	<form class="FilesList" action="{$ActionDeleteFiles}" onsubmit="if ( confirm('Wirklich löschen?') ) SubmitForm(this, '.RightColumn'); return false;" style="padding-top: 130px;">
		<div class="FixedHead">
			<a class="Button Right" href="javascript:" onclick="$(this).parents('.FilesList').hide().siblings('.FilesCreate').show()">Dateien hochladen</a>
			{?Directory}
			<a class="Button Right Light" href="javascript:" onclick="Load('.Columns', '{$ActionDeleteDirectory}')">Verzeichnis löschen</a>
			{/?Directory}
			{?Files}
			<button class="Button Right Delete" type="submit" title="Löschen"></button>
			<span style="float: right;">Markierte:</span>
			{/?Files}
			<h1>{$Directory}{?!Directory}{RootDirectory}{/?!Directory} <span style="color: #cccccc;">&bull; {$Files.Count} Dateien</span></h1>
		</div>
		{?Files}
			<table class="DataTable" cellspacing="0">
				<colgroup>
					<col width="1" />
					<col width="75" />
					<col />
					<col />
					<col width="1" />
				</colgroup>
				<tr>
					<th><input type="checkbox" onclick="$(this).parents('table').find('input[type=checkbox]').attr('checked', this.checked ? true : false)" /></th>
					<th>&nbsp;</th>
					<th>Name</th>
					<th class="AlignRight">Größe</th>
					<th>&nbsp;</th>
				</tr>
				{@Files}
					<tr>
						<td><input name="DeleteFiles[]" type="checkbox" value="{$RelativePath}" /></td>
						<td>
							{?Thumbnail}
							<img src="{$Thumbnail}" style="background: #eeeeee;" />
							{/?Thumbnail}
							{?!Thumbnail}
							{$Extension}
							{/?!Thumbnail}
						</td>
						<td>
							{?.Browser}
							<a href="javascript:" onclick="Browser.Select('{$._FilesUrl}{$RelativePath}');">{$Name}</a>
							{/?.Browser}
							{?!.Browser}
							<a href="{$._FilesUrl}{$RelativePath}" target="_blank">{$Name}</a>
							{/?!.Browser}
						</td>
						<td class="AlignRight">{$Size}</td>
						<td><button class="Button Right Delete" type="submit" onclick="$(this).parents('table').find('input[type=checkbox]').removeAttr('checked'); $(this).parents('tr').find('input[type=checkbox]').prop('checked', true);"></button></td>
					</tr>
				{/@Files}
			</table>
		{/?Files}
		
		{?!Files}
			<p>Das Verzeichnis ist leer.</p>
		{/?!Files}
	</form>
	
	<form class="FilesCreate" method="post" action="{$ActionCreateFiles}" style="display: none; padding-top: 130px;" enctype="multipart/form-data" target="UploadFrame{$_InstanceID}">
		<div class="FixedHead">
			<button class="Button Right" type="submit" onclick="$('body').addClass('Loading')">Hochladen</button>
			<a class="Button Right Light" href="javascript:" onclick="$(this).parents('.FilesCreate').hide().siblings('.FilesList').show()">Abbrechen</a>
			<h1>{$Directory}{?!Directory}{RootDirectory}{/?!Directory} <span style="color: #cccccc;">&bull; Dateien hochladen</span></h1>
		</div>
		<p>Maximale Dateigröße: {$MaximalFileSize}, maximale Größe aller Dateien: {$MaximalUploadSize}</p>
		<ul class="Form">
			<li>
				<label>Datei wählen</label>
				<button class="Button Right Delete" type="button" onclick="if ( $(this).siblings('input').val() ) $(this).parents('li').remove()"></button>
				<span class="Button Right">Durchsuchen...<input name="CreateFiles[]" type="file" onchange="if ( !$(this).parent().siblings('input').val() ) $(this).parents('ul').append($(this).parents('li').clone()); $(this).parent().siblings('input').val(this.value)" /></span>
				<input class="FileName" type="text" readonly />
			</li>
		</ul>
		<iframe name="UploadFrame{$_InstanceID}" height="1" width="1" onload="if ( $('body').hasClass('Loading') ) Load('.RightColumn', '{$ActionReload}')"></iframe>
	</form>
{/?BlockDirectory}