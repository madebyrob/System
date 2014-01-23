<?php

class Base_Access_Model_Group extends Base_Core_Model
{
	protected $_Table = 'Groups';
	
	protected $_UseAttributes = true;
	
	public function Loaded()
	{
		$this->Set('Name', $this->Translate('Group'.$this->Get('Key')));
	}
	
	public function CheckMembership( $PermittedGroups, $User = false )
	{
		$User = is_numeric($User) && $User > 0 ? $this->NewModel('Base/Access/User')->Load($User) : $this->Single('Base/Access/User');
		
		return count(array_intersect(explode(',', '0,'.$User->Get('Groups')), explode(',', $PermittedGroups))) > 0;
	}
}

?>