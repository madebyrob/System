<?php

class T4M_Core_Controller_EditAttributes extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['Attribute'] ) return false;
		
		$Attribute = $this->NewModel('Base/Core/Attribute');
		
		if ( $_POST['Attribute']['ID'] ) $Attribute->Load($_POST['Attribute']['ID']);
		
		$Attribute->MultiSet($_POST['Attribute']);
		
		if ( $Attribute->Save(false) ) $this->Hint($_POST['Attribute']['ID'] ? 'AttributeUpdated' : 'AttributeCreated', $Attribute->Get('Name'));
		
		return true;
	}
	
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$Attribute = $this->NewModel('Base/Core/Attribute');
			
			if ( $Attribute->Load($ID) && $Attribute->Delete(false) ) $this->Hint('AttributeDeleted', $Attribute->Get('Name'));
		}
		elseif ( $_POST['DeleteAttribute'] )
		{
			$Attributes = $this->NewCollection('Base/Core/Attribute');
			$Attributes->Set('Filter', array('ID' => array('in' => $_POST['DeleteAttribute'])));
			$Attributes->Load();
			
			foreach ( $Attributes->Items() as $Attribute )
			{
				if ( $Attribute->Delete(false) ) $this->Hint('AttributeDeleted', $Attribute->Get('Name'));
			}
		}
		
		return true;
	}
}

?>