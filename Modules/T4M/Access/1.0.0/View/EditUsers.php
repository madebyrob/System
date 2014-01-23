<?php

class T4M_Access_View_EditUsers extends Base_Core_View
{
	public function Process()
	{
		if ( is_numeric($_GET['User']) )
		{
			$User = $this->NewModel('Base/Access/User')->Load($_GET['User']);
			
			$this->MultiSet($User->Get());
			$this->Set('Attributes', $User->Attributes()->ToArray());
			$this->Set('BlockEdit', 1);
			$this->Set('ActionSave', $this->Action().'Save&User='.$_GET['User']);
			$this->Set('ActionDelete', $this->Action().'Delete/'.$_GET['User']);
		}
		else
		{
			$this->Set('BlockMain', 1);
			$this->Set('ActionSave', $this->Action().'Save');
			$this->Set('ActionCreate', $this->Single('Base/Structure/Article')->Get('Url'));
			$this->Set('ActionDelete', $this->Action().'Delete');
			
			$Users = $this->NewCollection('Base/Access/User');
			$Users->Set('OrderBy', array('Name' => 'ASC'));
			$Users->Load();
			
			foreach ( $Users->Items() as $User ) $User->Set('ActionOpen', $this->Single('Base/Structure/Article')->Get('Url').'?User='.$User->Get('ID'));
			
			$this->Set('Users', $Users->ToArray());
		}
	}
}

?>