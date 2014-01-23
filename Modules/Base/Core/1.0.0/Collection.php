<?php

class Base_Core_Collection extends Base_Core_Object
{
	protected $_Loaded = false;
	
	protected $_Items = array();
	
	protected $_Parent = NULL;
	
	protected $_ItemModel = NULL;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->Set('Key', 'ID');
	}
		
	public function Load()
	{
		if ( !$this->_ItemModel ) return false;
		
		$Items = $this->_ItemModel->LoadItems($this->Get());
		
		if ( !is_array($Items) ) return false;
		
		$this->_Items = $Items;
		
		$this->_Loaded = true;
		
		return $this;
	}
	
	public function ItemModel( $Selector = NULL )
	{
		if ( $Selector )
		{
			if ( !$this->ParseSelector($Selector) ) return false;
			
			$this->_ItemModel = $this->NewModel($Selector);
			
			return $this;
		}
		elseif ( $Selector === false ) return $this;
		
		return $this->_ItemModel;
	}
	
	public function Parent( $Parent = NULL )
	{
		if ( $Parent )
		{
			if ( !is_object($Parent) ) return false;
			
			$this->_Parent = $Parent;
			
			if ( $this->_ItemModel ) $this->_ItemModel->Parent($Parent);
			
			return $this;
		}
		
		return $this->_Parent;
	}
	
	public function Loaded()
	{
		return $this->_Loaded;
	}
	
	public function AddItem( $Item, $Key = NULL )
	{
		if ( !is_object($Item) ) return false;
		
		if ( $this->Parent() ) $Item->Parent($this->Parent());
		
		if ( is_string($Key) || is_numeric($Key) ) $this->_Items[$Key] = $Item;
		elseif ( $Key === NULL ) array_push($this->_Items, $Item);
		else return false;
		
		return $this;
	}
	
	public function AddItems( $Items )
	{
		if ( is_array($Items) ) $this->_Items = array_merge($this->_Items, $Items);
		else return false;
		
		return $this->_Items;
	}
	
	public function RemoveItem( $Key )
	{
		if ( isset($this->_Items[$Key]) ) unset($this->_Items[$Key]);
		
		return $this;
	}
	
	public function RemoveAll()
	{
		$this->_Items = array();
		
		return $this;
	}
	
	public function Item( $Key )
	{
		if ( isset($this->_Items[$Key]) ) return $this->_Items[$Key];
		
		return false;
	}
	
	public function Items( $Items = NULL )
	{
		if ( is_array($Items) )
		{
			$this->_Items = $Items;
			
			return $this;
		}
		elseif ( $Items !== NULL ) return false;
		
		return $this->_Items;
	}
	
	public function Current()
	{
		if ( !$this->Count() ) return NULL;
		elseif ( current($this->_Items) !== false ) return current($this->_Items);
		else return $this->First();
	}
	
	public function First()
	{
		return $this->Count() ? reset($this->_Items) : NULL;
	}
	
	public function Last()
	{
		return $this->Count() ? end($this->_Items) : NULL;
	}
	
	public function Prev()
	{
		return $this->Count() ? prev($this->_Items) : NULL;
	}
	
	public function Next()
	{
		return $this->Count() ? next($this->_Items) : NULL;
	}
	
	public function Keys()
	{
		return array_keys($this->_Items);
	}
	
	public function Count()
	{
		return is_array($this->_Items) ? count($this->_Items) : 0;
	}
	
	public function ToArray( $Attributes = false )
	{
		$Data = array();
		
		foreach ( $this->_Items as $Key => $Item )
		{
			$Data[$Key] = $Item->Get();
			
			if ( $Attributes && $Item->HasAttributes() ) $Data[$Key]['Attributes'] = $Item->Attributes()->ToArray();
		}
		
		return $Data;
	}
	
	public function Shuffle()
	{
		shuffle($this->_Items);
		
		return $this;
	}
	
	public function Reverse()
	{
		array_reverse($this->_Items);
		
		return $this;
	}
	
	public function Sort()
	{
		$SortTerms = func_get_args();
		$Function = '$Find = array(\'ä\', \'Ä\', \'ö\', \'Ö\', \'ü\', \'Ü\', \'ß\'); $Replace = array(\'a\', \'A\', \'o\', \'O\', \'u\', \'U\', \'ss\'); ';
			
		foreach ( $SortTerms as $SortTerm )
		{		
			$Types = array(
				'Loc' => 'strcmp(str_replace($Find, $Replace, $A->Get(\''.$SortTerm['Key'].'\')), str_replace($Find, $Replace, $B->Get(\''.$SortTerm['Key'].'\')))',
				'Str' => 'strcmp($A->Get(\''.$SortTerm['Key'].'\'), $B->Get(\''.$SortTerm['Key'].'\'))',
				'Stri' => 'strcasecmp($A->Get(\''.$SortTerm['Key'].'\'), $B->Get(\''.$SortTerm['Key'].'\'))',
				'Nat' => 'strnatcmp($A->Get(\''.$SortTerm['Key'].'\'), $B->Get(\''.$SortTerm['Key'].'\'))',
				'Nati' => 'strnatcasecmp($A->Get(\''.$SortTerm['Key'].'\'), $B->Get(\''.$SortTerm['Key'].'\'))',
				'Num' => '( $A->Get(\''.$SortTerm['Key'].'\') - $B->Get(\''.$SortTerm['Key'].'\') )'
			);
			
			$Function .= 'if ( $Result = '.( isset($SortTerm['Dir']) && $SortTerm['Dir'] == 'DESC' ? '-' : '' ).( $Types[$SortTerm['Type']] ? $Types[$SortTerm['Type']] : $Types['Nati'] ).' ) return $Result; ';
		}
		
		uasort($this->_Items, create_function('$A, $B', $Function));
		
		return $this;
	}
	
	public function Filter( $Key, $Value, $CaseSensitive = false )
	{
		$Results = $this->NewCollection(false)->Parent($this->Parent());
		
		foreach ( $this->_Items as $Item )
		{
			if ( !$Item->Get($Key) ) continue;
			
			if ( !$CaseSensitive && is_string($Item->Get($Key)) && is_string($Value) )
			{
				if ( strtolower($Item->Get($Key)) === strtolower($Value) ) $Results->AddItem($Item);
			}
			elseif ( $Item->Get($Key) === $Value ) $Results->AddItem($Item);
		}
		
		return $Results;
	}
}

?>