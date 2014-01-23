<?php

class Base_Structure_View_SitesList extends Base_Core_View
{
	public function Process()
	{
		$Sites = $this->NewCollection('Base/Structure/Site');
		$Sites->Set('Filter', array('Status' => array('in' => array(2, 3))));
		$Sites->Load();
		
		if ( $Site = $Sites->Item($this->Single('Base/Structure/Site')->Get('ID')) ) $Site->Set('Selected', 1);
		
		$this->Set('Sites', $Sites->ToArray());
	}
}

?>