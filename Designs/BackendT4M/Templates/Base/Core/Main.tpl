<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>{$_SystemName}</title>
		<link rel="stylesheet" type="text/css" href="{$_DesignUrl}CSS/Default.css" media="screen" />
		<script src="{$_DesignUrl}JS/jquery.js"></script>
		<script src="{$_DesignUrl}JS/jquery.mousewheel.js"></script>
		<script src="{$_DesignUrl}JS/main.js"></script>
		<script>
		
		Browser.BaseUrl = '{$_Url}';
		Browser.Types = {'default': '{$_SiteUrl}content/articles.html?Browser=1', 'files': '{$_SiteUrl}files/?Browser=1', 'image': '{$_SiteUrl}files/?Browser=1'};
		
		</script>
	</head>
	<body>
		<div class="Head">
			{@Head}{$Content.HTML}{/@Head}
			<div class="Messages">
				<ul>
					{@Messages}
					<li class="{$Type} New">{$Message.HTML}</li>
					{/@Messages}
					<li class="All">{ShowAll}</li>
				</ul>
			</div>
			<a class="Logo" href="{$_SiteUrl}">{$_SystemName}{?_UserStatus>1}<span class="Version">Version {$_SystemVersion}</span>{/?_UserStatus>1}</a>
		</div>
		<div class="Body Columns">
			{@Body}{$Content.HTML}{/@Body}
		</div>
		{@AfterBody}{$Content.HTML}{/@AfterBody}
		<div class="Browser">
			<div class="Title">
				<button class="Button Right" type="button" onClick="Browser.Close()">Abbrechen</button>
				<h1>Browser <span style="color: #cccccc;">&bull; <a href="javascript:" onClick="Load('.Browser .Content', Browser.Types.default)">Kategorien &amp; Artikel</a> &bull; <a href="javascript:" onClick="Load('.Browser .Content', Browser.Types.files)">Dateien</a></span></h1>
			</div>
			<div class="Content Columns"></div>
		</div>
		<div class="Loader"></div>
	</body>
</html>