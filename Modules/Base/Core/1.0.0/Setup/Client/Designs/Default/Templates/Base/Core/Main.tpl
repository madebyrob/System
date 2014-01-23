<!DOCTYPE html>
<html>
	<head>
		<title>{$_SiteTitlePrefix}{$_ArticleName}{$_SiteTitleSuffix}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<link rel="stylesheet" type="text/css" href="{$_DesignUrl}CSS/Base.css" media="all" />
		<link rel="stylesheet" type="text/css" href="{$_DesignUrl}CSS/Default.css" media="all" />
		<script type="text/javascript" src="{$_DesignUrl}JS/Main.js"></script>
	</head>
	<body>
		{?Messages}
		<ul>
			{@Messages}
			{?Type=Error}
			<li><b>{LabelError}</b> {$Message.HTML}</li>
			{/?Type=Error}
			
			{?Type=Warning}
			<li><b>{LabelWarning}</b> {$Message.HTML}</li>
			{/?Type=Warning}
			
			{?Type=Hint}
			<li><b>{LabelHint}</b> {$Message.HTML}</li>
			{/?Type=Hint}
			{/@Messages}
		</ul>
		{/?Messages}
		<h1>{$_ArticleHeadline}</h1>
		{$_ArticleContent.HTML}
	</body>
</html>