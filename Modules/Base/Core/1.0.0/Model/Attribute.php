<?php

class Base_Core_Model_Attribute extends Base_Core_Model
{
	protected $_Table = 'Attributes';
	
	public function Loaded()
	{
		$Model = $this->NewModel($this->Get('Model'));
		
		if ( $this->Get('Key') && $this->Get('Model') ) $this->Set('Name', $Model ? $Model->Translate('Attribute'.$Model->Name().$this->Get('Key')) : 'UNKNOWN MODEL');
	}
	
	public function LoadInstant( $Options )
	{
		$Attributes = $this->NewCollection();
		$Attributes->Set('Filter', $Options);
		$Attributes->Load();
		
		if ( $Attributes->Count() > 1 ) return false;
		
		if ( $Attributes->Count() == 1 ) return $Attributes->First();
		
		$Attribute = $this->NewModel();
		$Attribute->MultiSet($Options);
		$Attribute->Save(false);
		
		return $Attribute;
	}
	
	public function NewValue()
	{
		return $this->NewModel('Base/Core/AttributeValue')->Ready(true)->Set('Key', $this->Get('Key'))->Set('Type', $this->Get('Type'))->Set('Model', $this->Get('Model'))->Ready(false)->Set('Attribute', $this->Get('ID'));
	}
	
	public function Delete( $Hint = true )
	{
		$AttributeValues = $this->NewCollection('Base/Core/AttributeValue')->Set('Filter', array('Attribute' => $this->Get('ID')))->Load();
		
		foreach ( $AttributeValues->Items() as $AttributeValue ) $AttributeValue->Delete(false);
		
		return parent::Delete($Hint);
	}
}

?>