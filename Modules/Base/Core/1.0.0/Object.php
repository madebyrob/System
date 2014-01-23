<?php

class Base_Core_Object
{
	private $_Name;
	
	private $_Module;
	
	private $_ModuleGroup;
	
	protected $_Data = array();
	
	function __construct()
	{
		$Parts = explode('_', get_class($this));
		
		$this->_ModuleGroup = array_shift($Parts);
		$this->_Module = array_shift($Parts);
		array_shift($Parts);
		$this->_Name = implode('/', $Parts);
	}
	
	public function Set( $Key, $Value )
	{
		if ( method_exists($this, 'Set'.$Key) ) $Value = call_user_func(array($this, 'Set'.$Key), $Value);
			
		$this->_Data[$Key] = $Value;
		
		return $this;
	}
	
	public function MultiSet( $Data, $Prefix = '' )
	{
		if ( !is_array($Data) && !is_object($Data) ) return false;
		
		foreach ( $Data as $Key => $Value ) $this->Set($Prefix.$Key, $Value);
		
		return $this;
	}
	
	public function Remove( $Key )
	{
		if ( isset($this->_Data[$Key]) ) unset($this->_Data[$Key]);
		
		return $this;
	}
	
	public function Get( $Key = NULL )
	{
		if ( $Key !== NULL )
		{
			$Data = isset($this->_Data[$Key]) ? $this->_Data[$Key] : NULL;
			
			if ( $Key && method_exists($this, 'Get'.$Key) ) $Data = call_user_func(array($this, 'Get'.$Key), $Data);
			
			return $Data;
		}
		else return $this->_Data;
	}
	
	public function Clear()
	{
		$this->_Data = array();
	}
	
	public function Duplicate()
	{
		$Object = $this->NewModel();
		
		return $Object->MultiSet($this->Get());
	}
	
	public function Name()
	{
		return $this->_Name;
	}
	
	public function Module()
	{
		return $this->_Module;
	}
	
	public function ModuleGroup()
	{
		return $this->_ModuleGroup;
	}
	
	public function ParseSelector( $Selector )
	{
		if ( is_string($Selector) && preg_match('~^([a-z0-9]+)/([a-z0-9]+)/([a-z0-9]+(?:/[a-z0-9]+)*)$~i', $Selector, $Parts) )
		{
			return array(
				'ModuleGroup' => $Parts[1],
				'Module' => $Parts[2],
				'Name' => $Parts[3]
			);
		}
		
		return false;
	}
	
	public function Selector( $Selector = NULL )
	{
		if ( $Selector )
		{
			if ( $Selector = $this->ParseSelector($Selector) )
			{
				$this->_Name = $Selector['Name'];
				$this->_Module = $Selector['Module'];
				$this->_ModuleGroup = $Selector['ModuleGroup'];
				
				return $this;
			}
			else return false;
		}
		
		return $this->_ModuleGroup.'/'.$this->_Module.'/'.$this->_Name;
	}
	
	public function NewClass( $Type, $Selector = NULL )
	{
		if ( !$Selector ) $Selector = $this->Selector();
		
		if ( !$Selector = $this->ParseSelector($Selector) ) return false;
		
		$Class = $Selector['ModuleGroup'].'_'.$Selector['Module'].'_'.$Type.'_'.$Selector['Name'];
		
		if ( !@class_exists($Class, true) )
		{
			trigger_error('Class not found "'.$Class.'"', E_USER_WARNING);
			
			return false;
		}
		
		return new $Class();
	}
	
	public function Single( $Selector = NULL )
	{
		if ( !$Selector ) $Selector = $this->Selector();
		
		$Key = md5($Selector);
		$Model = System::Get('Singles', $Key);
		
		if ( $Model === NULL )
		{
			if ( !$Model = $this->NewModel($Selector) ) return false;
			
			System::Set('Singles', $Key, $Model);
			
			$Model = $Model->LoadSingle();
			
			System::Set('Singles', $Key, $Model);
		}
		
		return $Model;
	}
	
	public function ResetSingle( $Selector = NULL )
	{
		if ( !$Selector ) $Selector = $this->Selector();
		
		$Key = md5($Selector);
		
		System::Set('Singles', $Key, NULL);
	}
	
	public function NewModel( $Selector = NULL )
	{
		return $this->NewClass('Model', $Selector);
	}
	
	public function NewView( $Selector = NULL )
	{
		return $this->NewClass('View', $Selector);
	}
	
	public function NewController( $Selector = NULL )
	{
		return $this->NewClass('Controller', $Selector);
	}
	
	public function NewCollection( $Selector = NULL )
	{
		if ( $Selector === NULL ) $Selector = $this->Selector();
		
		$Collection = new Base_Core_Collection();
		
		return $Collection->ItemModel($Selector)->Parent($this);
	}
	
	public function Localization( $Module = NULL, $Key = NULL )
	{
		if ( !$Module ) $Module = $this->ModuleGroup().'/'.$this->Module();
		
		if ( !$Key && !($Key = $this->Single('Base/Structure/Site')->Get('Localization')) ) return false;
		
		return $this->NewModel('Base/Core/Localization')->Load($Module, $Key);
	}
	
	public function Translate( $Key, $Module = NULL, $Localization = NULL )
	{
		$Localization = $this->Localization($Module, $Localization);
		
		if ( $Localization && $Localization->Get($Key) ) return $Localization->Get($Key);
		
		return '{'.($Module ? $Module : $this->ModuleGroup().'/'.$this->Module()).'/'.$Key.'}';
	}
	
	public function Hint()
	{
		$Arguments = func_get_args();
		
		$Arguments[0] = $this->Translate($Arguments[0]);
		
		array_unshift($Arguments, $this->Selector());
		array_unshift($Arguments, 'Hint');
		
		call_user_func_array(array($this->NewController('Base/Core/Main'), 'Message'), $Arguments);
	}
	
	public function Warning()
	{
		$Arguments = func_get_args();
		
		$Arguments[0] = $this->Translate($Arguments[0]);
		
		array_unshift($Arguments, $this->Selector());
		array_unshift($Arguments, 'Warning');
		
		call_user_func_array(array($this->NewController('Base/Core/Main'), 'Message'), $Arguments);
	}
	
	public function Error()
	{
		$Arguments = func_get_args();
		
		$Arguments[0] = $this->Translate($Arguments[0]);
		
		array_unshift($Arguments, $this->Selector());
		array_unshift($Arguments, 'Error');
		
		call_user_func_array(array($this->NewController('Base/Core/Main'), 'Message'), $Arguments);
	}
}

?>