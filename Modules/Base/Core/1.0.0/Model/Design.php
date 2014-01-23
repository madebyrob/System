<?php

class Base_Core_Model_Design extends Base_Core_Model
{
	public function Load( $Key, $System = false )
	{
		$Path = ($System ? System::Path('System/Designs') : System::Path('Designs')).$Key.'/';
		
		if ( !preg_match('~^\w+$~i', $Key) || !is_dir($Path) ) return false;
		
		$this->Set('Key', $Key);
		$this->Set('Path', $Path);
		$this->Set('Url', System::Url($System ? 'System/Designs' : 'Designs').$Key.'/');
		
		return $this;
	}
	
	public function LoadSingle()
	{
		$Article = $this->Single('Base/Structure/Article');
		$Site = $this->Single('Base/Structure/Site');
		
		if ( !$Article ) return false;
		
		$Design = $Site->Get('Design');
		
		if ( !$Design ) $Design = 'Default';
		
		if ( !$this->Load($Design, $Site->Get('Backend') ? true : false) )
		{
			trigger_error('Design not found "'.$Design.'"!', E_USER_ERROR);
			
			exit();
		}
		
		return $this;
	}
	
	public function LoadItems( $Options )
	{
		$Systen = isset($Options['System']) && $Options['System'] ? true : false;
		$Path = $Systen ? System::Path('System/Designs') : System::Path('Designs');
		$Items = array();
		$Dir = opendir($Path);
		
		while ( $Key = readdir($Dir) )
		{
			if ( is_dir($Path.$Key) && $Item = $this->NewModel()->Load($Key, $Systen) ) $Items[$Key] = $Item;
		}
		
		return $Items;
	}
}

?>