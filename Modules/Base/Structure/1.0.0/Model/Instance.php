<?php

class Base_Structure_Model_Instance extends Base_Core_Model
{
	protected $_Table = 'Instances';
	
	protected $_UseAttributes = true;
	
	protected $_Contents;
	
	public function Contents( $Contents = NULL )
	{
		if ( $Contents !== NULL )
		{
			$this->_Contents = $Contents;
			
			return $this;
		}
		
		if ( !$this->_Contents )
		{
			# load contents
			$Contents = $this->NewCollection('Base/Structure/Content');
			
			if ( $this->Get('ID') )
			{
				$Contents->Set('Key', 'Key');
				$Contents->Set('Filter', array('Instance' => $this->Get('ID')));
				$Contents->Load();
				
				if ( $View = @$this->NewView($this->Get('View')) )
				{
					foreach ( $View->Contents() as $Content )
					{
						if ( !$Contents->Item($Content['Key']) ) $Contents->AddItem($this->NewModel('Base/Structure/Content')->Set('Key', $Content['Key'])->Set('Value', $Content['Value'])->Set('Instance', $this->Get('ID'))->Ready(true), $Content['Key']);
						
						$Contents->Item($Content['Key'])->Set('Name', $View->Translate('Content'.$View->Name().$Content['Key']))->Set('Type', $Content['Type']);
					}
				}
			}
			
			$this->_Contents = $Contents;
		}
		
		return $this->_Contents;
	}
	
	public function Article()
	{
		return $this->NewModel('Base/Structure/Article')->Load($this->Get('Atricle'));
	}
	
	public function View()
	{
		if ( !$this->Get('View') ) return false;
		
		$View = $this->NewView($this->Get('View'));
		
		if ( $View ) $View->Instance($this);
		
		return $View;
	}
	
	public function Delete( $Hint = true )
	{
		$Contents = $this->NewCollection('Base/Structure/Content')->Set('Filter', array('Instance' => $this->Get('ID')))->Load();
		
		foreach ( $Contents->Items() as $Content ) $Content->Delete(false);
		
		return parent::Delete($Hint);
	}
	
	public function ActionData()
	{
		if ( $Data = System::Get('ActionData', $this->Get('ID')) ) return $Data;
		
		return array();
	}
}

?>