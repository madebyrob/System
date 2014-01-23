<?php

class T4M_Structure_Controller_EditSites extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['Site'] ) return false;
		
		$Site = $this->NewModel('Base/Structure/Site');
		
		if ( $_POST['Site']['ID'] )
		{
			$Site->Load($_POST['Site']['ID']);
			
			if ( $_POST['Site']['Attributes'] )
			{
				foreach ( $_POST['Site']['Attributes'] as $Key => $Value )
				{
					if ( $Site->Attribute($Key) ) $Site->Attribute($Key)->Set('Value', $Value);
				}
			}
		}
		
		$Site->MultiSet($_POST['Site']);
		
		if ( $Site->Save(false) ) $this->Hint($_POST['Site']['ID'] ? 'SiteUpdated' : 'SiteCreated', $Site->Get('Name'));
	}
	
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$Site = $this->NewModel('Base/Structure/Site');
			
			if ( $Site->Load($ID) && $Site->Delete(false) ) $this->Hint('SiteDeleted', $Site->Get('Name'));
		}
		elseif ( $_POST['DeleteSite'] )
		{
			$Sites = $this->NewCollection('Base/Structure/Site');
			$Sites->Set('Filter', array('ID' => array('in' => $_POST['DeleteSite'])));
			$Sites->Load();
			
			foreach ( $Sites->Items() as $Site )
			{
				if ( $Site->Delete(false) ) $this->Hint('SiteDeleted', $Site->Get('Name'));
			}
		}
	}
}

?>