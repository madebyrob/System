<?php

class T4M_Access_View_EditGroups extends Base_Core_View
{
	public function Process()
	{
		if ( is_numeric($_GET['Group']) )
		{
			$Group = $this->NewModel('Base/Access/Group')->Load($_GET['Group']);
			
			$this->MultiSet($Group->Get());			
			$this->Set('Attributes', $Group->Attributes()->ToArray());
			$this->Set('BlockEdit', 1);
			$this->Set('ActionSave', $this->Action().'Save&Group='.$_GET['Group']);
			$this->Set('ActionDelete', $this->Action().'Delete/'.$_GET['Group']);
		}
		else
		{
			$this->Set('BlockMain', 1);
			$this->Set('ActionSave', $this->Action().'Save');
			$this->Set('ActionCreate', $this->Single('Base/Structure/Article')->Get('Url'));
			$this->Set('ActionDelete', $this->Action().'Delete');
			
			$Groups = $this->NewCollection('Base/Access/Group');
			$Groups->Set('OrderBy', array('Key' => 'ASC'));
			$Groups->Load();
			
			foreach ( $Groups->Items() as $Group ) $Group->Set('ActionOpen', $this->Single('Base/Structure/Article')->Get('Url').'?Group='.$Group->Get('ID'));
			
			$this->Set('Groups', $Groups->ToArray());
		}
	}
}

?>