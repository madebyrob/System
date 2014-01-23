<?php

class T4M_Access_Controller_EditGroups extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['Group'] ) return false;
		
		$Group = $this->NewModel('Base/Access/Group');
		
		if ( $_POST['Group']['ID'] )
		{
			$Group->Load($_POST['Group']['ID']);
			
			if ( $_POST['Group']['Attributes'] )
			{
				foreach ( $_POST['Group']['Attributes'] as $Key => $Value )
				{
					if ( $Group->Attribute($Key) ) $Group->Attribute($Key)->Set('Value', $Value);
				}
			}
		}
		
		$Group->MultiSet($_POST['Group']);
		
		if ( $Group->Save(false) ) $this->Hint($_POST['Group']['ID'] ? 'GroupUpdated' : 'GroupCreated', $Group->Get('Name'));
	}
	
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$Group = $this->NewModel('Base/Access/Group');
			
			if ( $Group->Load($ID) && $Group->Delete(false) ) $this->Hint('GroupDeleted', $Group->Get('Name'));
		}
		elseif ( $_POST['DeleteGroup'] )
		{
			$Groups = $this->NewCollection('Base/Access/Group');
			$Groups->Set('Filter', array('ID' => array('in' => $_POST['DeleteGroup'])));
			$Groups->Load();
			
			foreach ( $Groups->Items() as $Group )
			{
				if ( $Group->Delete(false) ) $this->Hint('GroupDeleted', $Group->Get('Name'));
			}
		}
	}
}

?>