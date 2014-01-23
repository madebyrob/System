<?php

class Base_Access_View_Login extends Base_Core_View
{
	public function Contents()
	{
		return array(
			array('Key' => 'UserSelection', 'Type' => 'Value', 'Value' => '0'),
			array('Key' => 'RedirectTo', 'Type' => 'Value', 'Value' => '')
		);
	}
	
	public function Process()
	{
		if ( $this->Content('UserSelection') )
		{
			$Users = array();
			
			foreach ( $this->NewModel('Base/Access/User')->Load() as $User )
			{
				if ( $User['ID'] > 1 ) $Users[] = $User;
			}
			
			$this->Set('Users', $Users);
		}
		
		$Site = $this->Single('Base/Structure/Site');
		$Article = $this->Single('Base/Structure/Article');
		
		$this->Set('RedirectTo', $this->Content('RedirectTo'));
		$this->Set('ActionLogin', $this->Single('Base/Core/Request')->Get('OriginalUrl').'?Action=/Base/Access/User/Login'.($Article->Get('Query') ? '&'.$Article->Get('Query') : ''));
		$this->Set('ActionLogout', $Site->Get('Url').$Site->Get('UrlLogout').'?Action=/Base/Access/User/Logout');
		$this->Set('SessionTimeout', System::Get('SessionTimeout'));
	}
}

?>