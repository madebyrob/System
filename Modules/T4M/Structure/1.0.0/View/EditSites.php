<?php

class T4M_Structure_View_EditSites extends Base_Core_View
{
	public function Process()
	{
		if ( is_numeric($_GET['Site']) )
		{
			$Site = $this->NewModel('Base/Structure/Site')->Load($_GET['Site']);
			
			$this->MultiSet($Site->Get());
			$this->Set('Attributes', $Site->Attributes()->ToArray());
			$this->Set('BlockEdit', 1);
			$this->Set('ActionSave', $this->Action().'Save&Site='.$_GET['Site']);
			$this->Set('ActionDelete', $this->Action().'Delete/'.$_GET['Site']);
		}
		else
		{
			$this->Set('BlockMain', 1);
			$this->Set('ActionSave', $this->Action().'Save');
			$this->Set('ActionCreate', $this->Single('Base/Structure/Article')->Get('Url'));
			$this->Set('ActionDelete', $this->Action().'Delete');
			
			$Sites = $this->NewCollection('Base/Structure/Site');
			$Sites->Set('OrderBy', array('Name' => 'ASC'));
			$Sites->Load();
			
			foreach ( $Sites->Items() as $Site ) $Site->Set('ActionOpen', $this->Single('Base/Structure/Article')->Get('Url').'?Site='.$Site->Get('ID'));
			
			$this->Set('Sites', $Sites->ToArray());
		}
	}
}

?>