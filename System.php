<?php

abstract class System
{
	private static $Data = array();
	
	public static function Init()
	{
		# system path
		self::Set('SystemPath', dirname(__FILE__).'/');
		
		# protocol
		self::Set('Protocol', isset($_SERVER['HTTPS']) ? 'https' : 'http');
		
		# host name
		self::Set('HostName', $_SERVER['HTTP_HOST']);
		
		# host url
		self::Set('HostUrl', self::Get('Protocol').'://'.self::Get('HostName'));
		
		# script name
		self::Set('ScriptName', basename($_SERVER['SCRIPT_NAME']));
		
		# base path
		self::Set('Path', getcwd().'/');
		
		# base url
		self::Set('Url', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/');
	}
			
	public static function Path( $Directory = '' )
	{
		$Path = self::Get('Path');
		
		$Parts = explode('/', $Directory);
		
		if ( $Parts[0] )
		{
			if ( $Parts[0] == 'System' )
			{
				$Path = self::Get('SystemPath');
				
				array_shift($Parts);
			}
			
			if ( $Parts )
			{
				if ( count($Parts) == 1 && $Directory = self::Get('Directories', $Parts[0]) ) $Path .= $Directory;
				else return false;
			}
		}
		
		return $Path;
	}
	
	public static function Url( $Directory = '', $Absolute = false )
	{
		$Url = self::Get('Url');
		
		$Parts = explode('/', $Directory);
		
		if ( $Parts[0] )
		{
			if ( $Parts[0] == 'System' )
			{
				$Url .= self::Get('ScriptName').'/System/';
				
				array_shift($Parts);
			}
			
			if ( $Parts )
			{
				if ( count($Parts) == 1 && $Directory = self::Get('Directories', $Parts[0]) ) $Url .= $Directory;
				else return false;
			}
		}
		
		return $Absolute ? self::Get('HostUrl').$Url : $Url;
	}
	
	static public function Get()
	{
		$Value = self::$Data;
		
		foreach ( func_get_args() as $Key )
		{
			if ( is_array($Value) && isset($Value[$Key]) ) $Value = $Value[$Key];
			elseif ( is_object($Value) && method_exists($Value, 'Get') ) $Value = $Value->Get($Key);
			else return NULL;
		}
		
		return $Value;
	}
	
	static public function Set()
	{
		$Arguments 	= func_get_args();
		
		if ( count($Arguments) < 2 ) return NULL;
		
		$Value = array_pop($Arguments);
		$Array = &self::$Data;
		
		foreach ( $Arguments as $Key )
		{
			if ( !isset($Array[$Key]) || !is_array($Array[$Key]) ) $Array[$Key] = array();
			
			$Array = &$Array[$Key];
		}
		
		$Array = $Value;
	}
	
	static public function Resolve( $Path, $Force = false, $BasePaths = NULL )
	{
		if ( $ResolvedPath = self::Get('Cache', 'Paths', 'Loaded', md5($Path)) ) return $ResolvedPath;
		
		if ( !$BasePaths ) $BasePaths = self::Get('BasePaths');
		
		foreach ( $BasePaths as $BasePath )
		{
			if ( file_exists($BasePath.$Path) )
			{
				$ResolvedPath = $BasePath.$Path;
				
				break;
			}
			else
			{
				if ( $Force ) break;
				
				preg_match('~^([^/]+/[^/]+/)(.+)$~', $Path, $Parts);
				
				if ( !is_dir($BasePath.$Parts[1]) ) continue;
				
				$Dirs = array();
				$Dir = opendir($BasePath.$Parts[1]);
				
				while ( $File = readdir($Dir) )
				{
					if ( !preg_match('~^\d+(\.\d+)+$~', $File) ) continue;
					
					$Dirs[] = $File;
				}
				
				if ( !$Dirs ) continue;
				
				natsort($Dirs);
				
				foreach ( array_reverse($Dirs) as $Dir )
				{
					$File = $BasePath.$Parts[1].$Dir.'/'.$Parts[2];
					
					if ( file_exists($File) )
					{
						$ResolvedPath = $File;
						
						break 2;
					}
				}
			}
		}
		
		if ( !$ResolvedPath ) return false;
		
		self::Set('Cache', 'Paths', md5($Path), $ResolvedPath);
		
		return $ResolvedPath;
	}
	
	static public function Load( $Path, $Return = false, $Force = false, $Paths = NULL )
	{
		if ( !$LoadPath = self::Resolve($Path, $Force, $Paths) )
		{
			if ( !$Return ) trigger_error('File not found "'.$Path.'"', E_USER_WARNING);
			
			return false;
		}
		
		if ( $Return ) return file_get_contents($LoadPath);
		
		include_once $LoadPath;
		
		return true;
	}
	
	static public function JsonRead()
	{
		$Paths = func_get_args();
		
		if ( !count($Paths) ) return false;
		
		$Return = array();
		
		foreach ( $Paths as $Path )
		{
			if ( !is_file($Path) || !($Content = file_get_contents($Path)) ) continue;
			
			$Data = json_decode($Content, true);
			
			# decode, check, merge translations
			if ( $Data === NULL ) trigger_error('JSON invalid: '.$Path, E_USER_WARNING);
			else $Return += $Data;
		}
		
		return $Return;
	}
	
	static public function MimeType( $Path )
	{
		preg_match('~\.([a-z0-9]{2,4})$~i', $Path, $File);
		
        switch ( strtolower($File[1]) )
        {
			case 'js':
				return 'application/x-javascript';
			
			case 'json':
				return 'application/json';
			
			case 'jpg':
			case 'jpeg':
			case 'jpe':
				return 'image/jpg';
			
			case 'png':
				return 'image/png';
			
			case 'gif':
				return 'image/gif';
			
			case 'bmp':
				return 'image/bmp';
			
			case 'tiff':
				return 'image/tiff';
			
			case 'css':
				return 'text/css';
			
			case 'xml':
				return 'application/xml';
			
			case 'doc':
			case 'docx':
				return 'application/msword';
			
			case 'xls':
			case 'xlt':
			case 'xlm':
			case 'xld':
			case 'xla':
			case 'xlc':
			case 'xlw':
			case 'xll':
				return 'application/vnd.ms-excel';
			
			case 'ppt':
			case 'pps':
				return 'application/vnd.ms-powerpoint';
			
			case 'rtf':
				return 'application/rtf';
			
			case 'pdf':
				return 'application/pdf';
			
			case 'html':
			case 'htm':
			case 'php':
				return 'text/html';
			
			case 'txt':
				return 'text/plain';
				
			case 'csv':
				return 'text/csv';
			
			case 'mpeg':
			case 'mpg':
			case 'mpe':
				return 'video/mpeg';
			
			case 'mp3':
				return 'audio/mpeg3';
			
			case 'wav':
				return 'audio/wav';
			
			case 'aiff':
			case 'aif':
				return 'audio/aiff';
			
			case 'avi':
				return 'video/msvideo';
			
			case 'wmv':
				return 'video/x-ms-wmv';
			
			case 'mov':
				return 'video/quicktime';
			
			case 'zip':
				return 'application/zip';
			
			case 'tar':
				return 'application/x-tar';
			
			case 'swf':
				return 'application/x-shockwave-flash';
			
			default:
				return 'unknown/'.$File[1];
		}
	}
	
	public static function ErrorHandler( $Type, $Message, $File, $Line )
	{
		$Types = array(
			1 => 'Error',
			2 => 'Warning',
			4 => 'Parse',
			8 => 'Notice',
			16 => 'E_CORE_ERROR',
			32 => 'E_CORE_WARNING',
			256 => 'E_USER_ERROR',
			512 => 'E_USER_WARNING',
			1024 => 'E_USER_NOTICE',
			2048 => 'Strict',
			4096 => 'E_RECOVERABLE_ERROR',
			8191 => 'E_ALL'
		);
		
		if ( !error_reporting() || $Type == 8 || $Type == 2048 ) return;
		
		self::Debug($Message, $Types[$Type], true);
		
		return true;
	}
	
	public static function Debug( $Data, $Title = '', $Error = false )
	{
		$Backtrace = array_reverse(debug_backtrace());
		$Info = $Title ? $Title.':'.chr(10).chr(10) : '';
		$i = 1;
		
		foreach ( $Backtrace as $File )
		{
			if ( $File['file'] )
			{
				$Info .= '#'.$i.' '.$File['file'].' ('.$File['line'].'), '.$File['class'].$File['type'].$File['function'].chr(10);
				
				++$i;
			}
		}
		
		$Info .= chr(10).print_r($Data, true);
		
		if ( self::Get('LogFile') ) file_put_contents(self::Get('LogFile'), $Info, FILE_APPEND);
		
		if ( self::Get('DebugRemotes') && in_array($_SERVER['REMOTE_ADDR'], self::Get('DebugRemotes')) )
		{
			echo '<textarea style="width: 100%; height: 500px; background: black; color: '.($Error ? '#ee0000' : 'white').'; border: 0; border-bottom: 1px dashed #999999; padding: 30px; font-family: \'Courier New\';" readonly="readonly">'.$Info.'</textarea>';
		}
	}
}

?>