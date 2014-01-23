<?php

class Base_Core_Model extends Base_Core_Object
{
	protected $_Ready = false;
	
	protected $_OriginalData = array();
	
	protected $_OriginalDataExclude = array();
	
	public $LoadOptions;
	
	public $CreateOptions;
	
	public $UpdateOptions;
	
	public $DeleteOptions;
	
	protected $_Table;
	
	protected $_Parent = NULL;
	
	protected $_UseAttributes = false;
	
	protected $_Attributes = NULL;
	
	public function __construct()
	{
		parent::__construct();
		
		if ( $this->UseAttributes() ) $this->Attributes($this->NewCollection('Base/Core/Attribute'));
	}
	
	public function LoadSingle()
	{
		# singleton init
		return $this;
	}
	
	public function Table( $Table = NULL )
	{
		if ( is_string($Table) ) $this->_Table = $Table;
		elseif ( $Table !== NULL ) return false;
		
		return $this->_Table;
	}
	
	public function Load( $Value, $Key = 'ID' )
	{
		if ( !$this->Table() ) return false;
		
		$Data = $this->NewModel('Base/Core/Data');
		$Data->Set('Tables', array($this->Table()));
		$Data->Set('Filter', array($Key => $Value));
		$Data->Set('Limit', array(0, 1));
		
		if ( $this->LoadOptions ) $Data->MultiSet($this->LoadOptions);
		
		if ( !$Data->Load() ) return false;
		
		if ( !$Data = $Data->Next() ) return false;
		
		$this->MultiSet($Data);
		$this->Ready(true);
		$this->LoadAttributes();
		$this->Loaded();
		
		return $this;
	}
	
	public function Loaded()
	{
	}
	
	public function Ready( $State = NULL )
	{
		if ( $State === NULL ) return $this->_Ready;
		
		$this->_Ready = $State ? true : false;
		
		return $this;
	}
	
	public function LoadItems( $Options )
	{
		$Data = $this->NewModel('Base/Core/Data');
		$Data->Set('Tables', array($this->Table()));
		
		if ( $Options['Columns'] && !in_array($Options['Key'], $Options['Columns']) ) $Options['Columns'][] = $Options['Key'];
		
		$Data->MultiSet($Options);
		$Data->Load();
		
		$Items = array();
		
		while ( $Item = $Data->Next() )
		{
			$Key = $Options['Key'] ? $Item[$Options['Key']] : count($Items);
			
			$Items[$Key] = $this->NewModel();
			$Items[$Key]->Parent($this->Parent());
			$Items[$Key]->MultiSet($Item);
			$Items[$Key]->Ready(true);
			$Items[$Key]->Loaded();
		}
		
		if ( $Options['Attributes'] && count($Items) ) $this->LoadAttributes($Items, $Options['Attributes']);
		
		return $Items;
	}
	
	public function Attributes( $Attributes = NULL )
	{
		if ( is_object($Attributes) ) $this->_Attributes = $Attributes;
		elseif ( $Attributes !== NULL ) return false;
		
		return $this->_Attributes;
	}
	
	public function LoadAttributes( $Items = NULL, $Keys = NULL )
	{
		if ( $this->UseAttributes() )
		{
			if ( $Items === NULL ) $Items = array($this->Get('ID') => $this);
			
			if ( !is_array($Items) || !count($Items) ) return false;
			
			$Filter = array('Model' => $this->Selector());
			$RawAttributes = array();
			$Parents = array_keys($Items);
			
			if ( is_array($Keys) ) $Filter['Key'] = array('in' => $Keys);
			
			$Attributes = $this->NewCollection('Base/Core/Attribute');
			$Attributes->Set('Columns', array('Key', 'Model', 'Type'));
			$Attributes->Set('Filter', $Filter);
			$Attributes->Load();
			
			if ( $Attributes->Count() )
			{
				foreach ( $Items as $Item )
				{
					foreach ( $Attributes->Items() as $Attribute )
					{
						$CombinedAttribute = $this->NewModel('Base/Core/AttributeValue');
						$CombinedAttribute->Set('Attribute', $Attribute->Get('ID'));
						$CombinedAttribute->Set('Value', '');
						$CombinedAttribute->Ready(true);
						$CombinedAttribute->Set('Name', $Attribute->Get('Name'));
						$CombinedAttribute->Set('Key', $Attribute->Get('Key'));
						$CombinedAttribute->Set('Model', $Attribute->Get('Model'));
						$CombinedAttribute->Set('Type', $Attribute->Get('Type'));
						
						$Item->AddAttribute($CombinedAttribute);
					}
				}
				
				$AttributeValues = $this->NewCollection('Base/Core/AttributeValue');
				$AttributeValues->Set('Filter', array('Parent' => array('in' => $Parents), 'Attribute' => array('in' => $Attributes->Keys())));
				$AttributeValues->Load();
				
				foreach ( $AttributeValues->Items() as $AttributeValue )
				{
					$Attribute = $Attributes->Item($AttributeValue->Get('Attribute'));
					$Item = $Items[$AttributeValue->Get('Parent')];
					
					$CombinedAttribute = $Item->Attribute($Attribute->Get('Key'));
					$CombinedAttribute->Ready(false);
					$CombinedAttribute->MultiSet($AttributeValue->Get());
					$CombinedAttribute->Ready(true);
					$CombinedAttribute->Loaded();
					
					$Item->Set('Attribute'.$CombinedAttribute->Get('Key'), $CombinedAttribute->Get('Value'));
				}
			}
		}
		
		return $Items;
	}
	
	public function AddAttribute( $Attribute )
	{
		if ( !$this->UseAttributes() || !$Attribute->Get('Key') ) return false;
		
		$ReadyState = $Attribute->Ready();
		
		$Attribute->Ready(false)->Set('Parent', $this->Get('ID'))->Ready($ReadyState)->Parent($this);
		
		$this->Attributes()->AddItem($Attribute, $Attribute->Get('Key'));
		
		$this->Set('Attribute'.$Attribute->Get('Key'), $Attribute->Get('Value'));
		
		return $this;
	}
	
	public function RemoveAttribute( $Key )
	{
		if ( $this->Attributes()->Item($Key) ) $this->Attributes()->RemoveItem($Key);
		
		return $this;
	}
	
	public function Attribute( $Key )
	{
		if ( !$this->HasAttributes() ) return false;
		
		if ( $Attribute = $this->Attributes()->Item($Key) ) return $Attribute;
		
		return NULL;
	}
	
	public function AttributeValue( $Key )
	{
		if ( $Attribute = $this->Attribute($Key) ) return $Attribute->Get('Value');
		
		return NULL;
	}
	
	public function UseAttributes()
	{
		return $this->_UseAttributes;
	}
	
	public function HasAttributes()
	{
		return $this->UseAttributes() && $this->Attributes()->Count() ? true : false;
	}
	
	public function BeforeSave()
	{
		$User = $this->Single('Base/Access/User');
		$Time = time();
		
		if ( $this->Get('ID') )
		{
			$this->Set('UpdateTime', $Time);
			$this->_OriginalData['UpdateTime'] = $Time;
			
			$this->Set('UpdateUser', $User->Get('ID'));
			$this->_OriginalData['UpdateUser'] = $User->Get('ID');
		}
		else
		{
			$this->Set('CreateTime', $Time);
			$this->_OriginalData['CreateTime'] = $Time;
			
			$this->Set('CreateUser', $User->Get('ID'));
			$this->_OriginalData['CreateUser'] = $User->Get('ID');
		}
	}
	
	public function Save( $Hint = true )
	{
		if ( !$this->Table() ) return false;
		
		$this->Ready(true);
		$this->BeforeSave();
		
		$Values = array();
		
		foreach ( $this->_OriginalData as $Key => $Value )
		{
			if ( is_array($this->Get($Key)) ) $Values[$Key] = json_encode($this->Get($Key));
			else $Values[$Key] = $this->Get($Key);
		}
		
		$Data = $this->NewModel('Base/Core/Data');
		$Data->Tables(array($this->Table()));
		$Data->Data($Values);
		
		if ( $this->Get('ID') )
		{
			$Data->Type('UPDATE');
			$Data->Filter(array('ID' => $this->_OriginalData['ID']));
			
			if ( $this->UpdateOptions ) $Data->MultiSet($this->UpdateOptions);
		}
		else
		{
			$Data->Type('INSERT');
			
			if ( $this->CreateOptions ) $Data->MultiSet($this->CreateOptions);
		}
		
		if ( !$Data->Load() ) return false;
		
		if ( !$this->Get('ID') )
		{
			$this->Set('ID', $Data->InsertID());
			$this->_OriginalData['ID'] = $Data->InsertID();
			
			if ( $Hint ) $this->Hint('CreateSuccess');
			
			$this->Loaded();
		}
		elseif ( $Hint ) $this->Hint('UpdateSuccess');
		 
		if ( $this->HasAttributes() )
		{
			foreach ( $this->Attributes()->Items() as $Attribute ) $Attribute->Save(false);
		}
		
		return $this;
	}
	
	# delete methods
	public function Delete( $Hint = true )
	{
		if ( !$this->Table() || !$this->_OriginalData['ID'] ) return false;
		
		$Data = $this->NewModel('Base/Core/Data');
		$Data->Type('DELETE');
		$Data->Tables(array($this->Table()));
		$Data->Filter(array('ID' => $this->_OriginalData['ID']));
		
		if ( $this->DeleteOptions ) $Data->MultiSet($this->DeleteOptions);
		
		if ( !$Data->Load() || !$Data->AffectedRows() ) return false;
		
		if ( $this->UseAttributes() )
		{
			if ( !$this->HasAttributes() ) $this->LoadAttributes();
			
			if ( $this->HasAttributes() )
			{
				foreach ( $this->Attributes()->Items() as $Attribute ) $Attribute->Delete(false);
			}
		}
		
		if ( $Hint ) $this->Hint('DeleteSuccess');
		
		return $this;
	}
	
	# get attributes in no data found
	public function Get( $Key = NULL, $IgnoreAttributes = false )
	{
		$Value = parent::Get($Key);
		
		if ( $this->_UseAttributes && !$IgnoreAttributes )
		{
			if ( $Key === NULL )
			{
				foreach ( $this->Attributes()->Items() as $Attribute )
				{
					if ( !isset($Value[$Attribute->Get('Key')]) ) $Value[$Attribute->Get('Key')] = $Attribute->Get('Value');
				}
			}
			elseif ( $Value === NULL && $this->Attribute($Key) )
			{
				$Value = $this->Attribute($Key)->Get('Value');
			}
		}
		
		return $Value;
	}
	
	# fill original data
	public function Set( $Key, $Value )
	{
		if ( $this->_UseAttributes && $this->Get($Key, true) === NULL && $this->Attribute($Key) )
		{
			$Value = $this->Attribute($Key)->Set('Value', $Value);
			
			return $this;
		}
		else
		{
			$Return = parent::Set($Key, $Value);
			
			if ( !$this->Ready() && !in_array($Key, $this->_OriginalDataExclude) ) $this->_OriginalData[$Key] = $this->_Data[$Key];
			
			return $Return;
		}
	}
	
	# remove original data
	public function Remove( $Key )
	{
		if ( !$this->Ready() && isset($this->_OriginalData[$Key]) ) unset($this->_OriginalData[$Key]);
		
		return parent::Remove($Key);
	}
	
	public function Clear()
	{
		parent::Clear();
		
		$this->_OriginalData = array();
		$this->Ready(false);
		
		return $this;
	}
	
	public function Parent( $Parent = NULL )
	{
		if ( $Parent )
		{
			if ( !is_object($Parent) ) return false;
			
			$this->_Parent = $Parent;
			
			return $this;
		}
		
		return $this->_Parent;
	}
	
	public function OriginalData( $Data )
	{
		foreach ( $Data as $Key ) $this->_OriginalData[$Key] = 0;
		
		return $this;
	}
}

?>