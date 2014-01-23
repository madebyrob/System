<?php

class Base_Core_Controller_Panel extends Base_Core_Controller
{
	public $DefaultActionView = '';
	
	public $Conditions = '1';
	
	public $Model = NULL;
	
	public $EarlyActions = array('Load', 'DefaultAction', 'Create', 'Update', 'Delete');
	
	public function Load( $Conditions = '1', $SortTerm = 'ID', $SortDir = 'ASC', $Offset = 0 )
	{
		$Items = $this->NewCollection($this->NewModel);
#		$Items->Set('Filter', array('DeleteTime' => 0));
		$Items->Set('OrderBy', array($SortTerm => $SortDir == 'DESC' ? 'DESC' : 'ASC'));
		$Items->Set('Limit', array(0, 1000));
		$Items->Set('Attributes', true);
		
		if ( !$Items->Load() ) return false;
		
		#$this->Conditions
		#$this->LoadOptions()
		
		return $Items;
	}
	
	public function LoadOptions()
	{
		return array();
	}
	
	public function AjaxLoad( $Conditions = '1', $SortTerm, $SortDir, $Offset )
	{
		$Items = $this->Load($Conditions, $SortTerm, SortDir, $Offset);
		
		exit($Items->Count() ? json_encode($Items->ToArray()) : '');
	}
	
	public function DefaultAction( $Namespace, $ID )
	{
		if ( $this->DefaultActionView && $View = $this->NewView($this->DefaultActionView) )
		{
			$View->Set('ParentNamespace', $Namespace);
			$View->Set('ID', $ID);
			$View->Process();
			
			$Data = array(
				'ID' => $ID,
				'Content' => $View->Render()
			);
		}
		else $Data = array('Error' => parent::Translate('UnknownView', $this->DefaultActionView));
		
		exit(json_encode($Data));
	}
	
	public function Create()
	{
		$Data = array();
		
		if ( isset($_POST['Create']) )
		{
			$Items = $this->NewCollection($this->NewModel);
			
			foreach ( $_POST['Create'] as $Key => $Values )
			{
				# avoid zero to force json object
				++$Key;
				
				$Item = $this->NewModel($this->NewModel);
				$Item->MultiSet($Values);
				
				if ( $Item->Save(false) ) $Items->AddItem($Item, $Key);
			}
			
			$Data = $Items->ToArray();
		}
		
		exit(json_encode($Data));
	}
	
	public function Update()
	{
		$Data = array();
		
		if ( isset($_POST['Update']) )
		{
			$Items = $this->NewCollection($this->NewModel);
			$Items->Set('Filter', array('ID' => array('in' => array_keys($_POST['Update']))));
			
			if ( $Items->Load() )
			{
				foreach ( $Items->Items() as $Key => $Item )
				{
					$Item->MultiSet($_POST['Update'][$Key]);
					
					if ( !$Item->Save(false) ) $Items->RemoveItem($Key);
				}
			}
			
			$Data = $Items->ToArray();
		}
		
		exit(json_encode($Data));
	}
	
	public function Delete()
	{
		$Data = array();
		
		if ( isset($_POST['Delete']) )
		{
			$Items = $this->NewCollection($this->NewModel);
			$Items->Set('Filter', array('ID' => array('in' => array_keys($_POST['Delete']))));
			
			if ( $Items->Load() )
			{
				foreach ( $Items->Items() as $Key => $Item )
				{
					if ( $Item->Delete(false) ) $Data[$Item->Get('ID')] = 1;
				}
			}
		}
		
		exit(json_encode($Data));
	}
}

?>