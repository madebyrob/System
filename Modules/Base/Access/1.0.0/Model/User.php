<?php

class Base_Access_Model_User extends Base_Core_Model
{
	protected $_Table = 'Users';
	protected $_ExpireTime = 1800;
	protected $_UseAttributes = true;
	
	public function LoadSingle()
	{
		$Session = $this->Single('Base/Access/Session');
		
		if ( $Session->Get('User') && $this->Load($Session->Get('User')) && $this->Get('Status') == 1 )
		{
			$this->Warning('SessionTimeout', $this->ExpireTime());
			$this->Clear();
		}
		
		if ( $this->IsLoggedIn() )
		{
			$this->Set('LastActivity', time());
			$this->Save(false);
		}
		else
		{
			if ( $Session->Get('User') ) $this->Single('Base/Access/Session')->Remove('User')->Save(false);
			
			$this->MultiSet(array('ID' => '0', 'Key' => 'guest', 'Name' => '{Guest}', 'Status' => 1, 'Groups' => '0'));
		}
		
		return $this;
	}
	
	public function Loaded()
	{
		if ( $this->Get('Status') == 2 && ( $this->Get('LastActivity')+$this->ExpireTime() ) < time() ) $this->Set('Status', 1);
	}
	
	public function SetPassword( $Password )
	{
		if ( !$this->Ready() && $this->Get('ID') ) return $Password;
		
		if ( $Password == '' ) return $this->_OriginalData['Password'];
		
		return $this->CryptPassword($Password);
	}
	
	public function CryptPassword( $Password )
	{
		return crypt($Password);
	}
	
	public function ExpireTime()
	{
		$ExpireTime = $this->Single('Base/Structure/Site')->Get('UserExpireTime');
		
		if ( !$ExpireTime ) $ExpireTime = $this->_ExpireTime;
		
		return $ExpireTime;
	}
	
	public function IsLoggedIn()
	{
		return $this->Get('Status') == 2;
	}
}

?>