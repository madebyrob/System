<?php

# config php
ini_set('error_reporting', E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);
ini_set('pcre.backtrack_limit', 1000000);

set_time_limit(60);

# load system class
include dirname(__FILE__).'/System.php';

# error handler
set_error_handler(array(System, 'ErrorHandler'), E_ALL);

# config system class
System::Set('Name', 'robmade CMS');
System::Set('Version', '1.0');
System::Set('Directories', array('Designs' => 'Designs/', 'Files' => 'Files/', 'Filters' => 'Filters/', 'Cache' => 'Cache/', 'Temp' => 'Temp/', 'Modules' => 'Modules/', 'External' => 'External/'));
System::Set('BackendSite', 'admin');

# load config if exists
if ( is_file('config.php') ) include 'config.php';

# init system
System::Init();
System::Set('BasePaths', array(System::Path('System/Modules'), System::Path('Modules')));

# autoload
function __autoload( $Class )
{
	System::Load(str_replace('_', '/', $Class).'.php');
}

# magic quotes fix
function StripSlashesDeep( $Value )
{
	return is_array($Value) ? array_map('StripSlashesDeep', $Value) : stripslashes($Value);
}

if ( get_magic_quotes_gpc() )
{
	$_GET = StripSlashesDeep($_GET);
	$_POST = StripSlashesDeep($_POST);
	$_COOKIE = StripSlashesDeep($_COOKIE);
}

# request routing
if ( preg_match('~^'.preg_quote(System::Get('Url').System::Get('ScriptName')).'/(System/[^/]+)/([^?]+)~', $_SERVER['REQUEST_URI'], $Matches) )
{
	if ( ($Path = System::Path($Matches[1])) && is_file($Path.$Matches[2]) )
	{
		header('Content-Type: '.System::MimeType($Matches[2]));
		
		readfile($Path.$Matches[2]);
	}
	else
	{
		header('HTTP/1.0 404 Not Found');
		
		trigger_error('Unknown file: '.$Matches[0], E_USER_WARNING);
	}
}
elseif ( !System::Get('DatabaseHost') || !System::Get('DatabaseUser') || !System::Get('DatabasePassword') || !System::Get('DatabaseName') )
{
	# run setup
	$Setup = new Base_Core_View_Setup();
	$Setup->Process();
	$Setup->Render();
}
else
{
	# run main controller
	$Controller = new Base_Core_Controller_Main();
	$Controller->ProcessUrl();
}

?>