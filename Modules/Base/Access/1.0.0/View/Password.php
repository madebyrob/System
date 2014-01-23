<?php

class Base_Access_View_Password extends Base_Core_View
{
	public function Process()
	{	
		$this->Set('Action', $this->Action('Base/Access/User').'Password');
		$this->Set('EmailAddress', $_POST['EmailAddress']);
		
		if ( $this->Instance() && ($ActionData = $this->Instance()->ActionData()) ) $this->MultiSet($ActionData);
	}
}

?>