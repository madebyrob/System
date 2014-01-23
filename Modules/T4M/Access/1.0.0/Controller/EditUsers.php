<?php

class T4M_Access_Controller_EditUsers extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['User'] ) return false;
		
		$User = $this->NewModel('Base/Access/User');
		
		if ( $_POST['User']['ID'] )
		{
			$User->Load($_POST['User']['ID']);
			
			if ( $_POST['User']['Attributes'] )
			{
				foreach ( $_POST['User']['Attributes'] as $Key => $Value )
				{
					if ( $User->Attribute($Key) ) $User->Attribute($Key)->Set('Value', $Value);
				}
			}
		}
		
		$User->MultiSet($_POST['User']);
		
		if ( $User->Save(false) ) $this->Hint($_POST['User']['ID'] ? 'UserUpdated' : 'UserCreated', $User->Get('Name'));
	}
		
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$User = $this->NewModel('Base/Access/User');
			
			if ( $User->Load($ID) && $User->Delete(false) ) $this->Hint('UserDeleted', $User->Get('Name'));
		}
		elseif ( $_POST['DeleteUser'] )
		{
			$Users = $this->NewCollection('Base/Access/User');
			$Users->Set('Filter', array('ID' => array('in' => $_POST['DeleteUser'])));
			$Users->Load();
			
			foreach ( $Users->Items() as $User )
			{
				if ( $User->Delete(false) ) $this->Hint('UserDeleted', $User->Get('Name'));
			}
		}
	}
}

?>