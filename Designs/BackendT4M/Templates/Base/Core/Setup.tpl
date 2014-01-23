<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>{$_SystemName} {TitleSetup}</title>
		<link rel="stylesheet" type="text/css" href="{$_DesignUrl}CSS/Default.css" media="screen" />
		<script src="{$_DesignUrl}JS/jquery.js"></script>
		<script src="{$_DesignUrl}JS/main.js"></script>
		<style type="text/css">
		
		
		.Wrapper { width: 500px; margin: 0 auto; }
		.Wrapper h1 { text-align: center; }
		
		</style>
	</head>
	<body>
		<div class="Wrapper">
			<div class="Logo">{$_SystemName}<div class="Version">Version {$_SystemVersion}</div></div>
			<form method="post" enctype="multipart/form-data">
				{?Step>1}
				<input name="Localization" type="hidden" value="{$_LocalizationKey}" />
				{/?Step>1}

				{?Step=1}
				<h1>{TitleSetup}</h1>
				<select name="Localization">
					{@Localizations}
					<option value="{$Key}"{?Selected} selected="selected"{/?Selected}>{$Name}</option>
					{/@Localizations}
				</select>
				<button class="Button Accept" name="Step" type="submit" title="{ButtonContinue}" value="1"></button>
				{/?Step=1}

				{?Step=2}
				<h1>{TitleSetupCheck}</h1>
				<p>{TextSetupCheck}</p>
				{?Errors}
				<ul>
					{@Errors}
					<li>{$_Value}</li>
					{/@Errors}
				</ul>
				{/?Errors}
				
				{?Warnings}
				<ul>
					{@Warnings}
					<li>{$_Value}</li>
					{/@Warnings}
				</ul>
				{/?Warnings}
				
				{?!Errors}<button class="Button Accept" name="Step" type="submit" title="{ButtonContinue}" value="2"></button>{/?!Errors}
				{/?Step=2}
				
				{?Step=3}
				<h1>{TitleSetupInfo}</h1>
				<p>{TextSetupInfo}</p>

				<h2>{TitleAdministration}</h2>
				<p>
					<label>{LabelAdminPassword}<input name="Info[Password]" type="password" value="{$Password}" /></label>
					{?ErrorPassword}(!){/?ErrorPassword}
				</p>
				<p>
					<label>{LabelRepeatPassword}<input name="Info[PasswordConfirm]" type="password" value="{$PasswordConfirm}" /></label>
					{?ErrorPasswordConfirm}(!){/?ErrorPasswordConfirm}
				</p>
				{?ErrorPasswordsNotEqual}(!){/?ErrorPasswordsNotEqual}
				
				<h2>{TitleDatabase}</h2>
				{?ErrorDatabaseConnection}Keine Verbindung!{/?ErrorDatabaseConnection}
				<p>
					<label>{LabelDatabaseHost}<input name="Info[DatabaseHost]" type="text" value="{$DatabaseHost}{?!DatabaseHost}localhost{/?!DatabaseHost}" /></label>
					{?ErrorDatabaseHost}(!){/?ErrorDatabaseHost}
				</p>
				<p>
					<label>{LabelDatabasePort}<input name="Info[DatabasePort]" type="text" value="{$DatabasePort}{?!DatabasePort}3306{/?!DatabasePort}" /></label>
					{?ErrorDatabasePort}(!){/?ErrorDatabasePort}
				</p>
				<p>
					<label>{LabelDatabaseName}<input name="Info[DatabaseName]" type="text" value="{$DatabaseName}" /></label>
					{?ErrorDatabaseName}(!){/?ErrorDatabaseName}
				</p>
				<p>
					<label>{LabelDatabaseUser}<input name="Info[DatabaseUser]" type="text" value="{$DatabaseUser}" /></label>
					{?ErrorDatabaseUser}(!){/?ErrorDatabaseUser}
				</p>
				<p>
					<label>{LabelDatabasePassword}<input name="Info[DatabasePassword]" type="password" value="{$DatabasePassword}" /></label>
					{?ErrorDatabasePassword}(!){/?ErrorDatabasePassword}
				</p>
				<p>
					<label>{LabelDatabaseTablePrefix}<input name="Info[DatabaseTablePrefix]" type="text" value="{$DatabaseTablePrefix}" /></label>
				</p>

				<button class="Button Accept" name="Step" type="submit" title="{ButtonContinue}" value="3"></button>
				{/?Step=3}
				
				{?Step=4}
				<h1>{TitleSetupComplete}</h1>
				<p>{TextSetupComplete}</p>
				<a href="{$_Url}">Weiter</a>
				{/?Step=4}
			</form>
		</div>
	</body>
</html>
