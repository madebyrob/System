<?php

class Base_Core_Model_Localization extends Base_Core_Model
{
	private $_Directory = 'Localization/';
	
	public function Load( $Module, $Key )
	{
		if ( !$Module || !$Key ) return false;
		
		$File = $Module.'/'.$this->Directory().$Key.'.json';
		
		if ( $Localization = System::Get('Cache', 'Localizations', md5($Module.'/'.$Key)) ) return $Localization;
		
		$Paths = array();
		
		foreach ( System::Get('BasePaths') as $Path )
		{
			if ( $ResolvedPath = System::Resolve($File, false, array($Path)) ) $Paths[] = $ResolvedPath;
		}
		
		if ( !count($Paths) ) return false;
		
		$this->Set('Key', $Key);
		$this->Set('Module', $Module);
		$this->Set('Paths', $Paths);
		$this->MultiSet(call_user_func_array(array(System, 'JsonRead'), $Paths));
		
		System::Set('Cache', 'Localizations', md5($Module.'/'.$Key), $this);
		
		return $this;
	}
	
	public function LoadItems( $Options )
	{
		if ( !$Options['Module'] ) return false;
		
		$Path = System::Resolve($Options['Module'].'/'.$this->Directory());
		$Items = array();
		$Dir = opendir($Path); 
		
		while ( $File = readdir($Dir) )
		{
			if ( !is_file($Path.$File) ) continue;
			
			$Key = str_replace('.json', '', $File);
			
			if ( $Item = $this->NewModel()->Load($Options['Module'], $Key) ) $Items[$Key] = $Item;
		}
		
		return $Items;
	}
	
	public function Directory()
	{
		return $this->_Directory;
	}
}

?>