<?php

class Base_Core_Model_Layout extends Base_Core_Model
{
	private $_Directory = 'Layouts/';
	
	public function Load( $Key )
	{
		$Design = $this->Single('Base/Core/Design');
		$Path = $Design->Get('Path').$this->Directory().$Key.'.json';
		
		if ( !$Design || !preg_match('~^\w+$~i', $Key) || !is_file($Path) ) return false;
		
		$this->MultiSet(System::JsonRead($Path));
		$this->Set('Key', $Key);
		$this->Set('Path', $Path);
		
		return $this;
	}
	
	public function LoadSingle()
	{
		$Article = $this->Single('Base/Structure/Article');
		
		if ( !$Article ) return false;
		
		$Layout = $Article->Get('Layout');
		
		if ( !$Layout ) $Layout = $Article->Get('AutoLayout');
		if ( !$Layout ) $Layout = 'Default';
		
		return $this->Load($Layout);
	}
	
	public function LoadItems( $Options )
	{
		$Design = $this->Single('Base/Core/Design');
		
		if ( !$Design ) return false;
		
		$Path = $Design->Get('Path').$this->Directory();
		$Items = array();
		$Dir = opendir($Path); 
		
		while ( $File = readdir($Dir) )
		{
			if ( !is_file($Path.$File) ) continue;
			
			$Key = str_replace('.json', '', $File);
			
			if ( $Item = $this->NewModel()->Load($Key) ) $Items[$Key] = $Item;
		}
		
		return $Items;
	}
	
	public function Directory()
	{
		return $this->_Directory;
	}
}

?>