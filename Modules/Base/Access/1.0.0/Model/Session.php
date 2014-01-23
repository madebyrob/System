<?php

class Base_Access_Model_Session extends Base_Core_Model
{
	protected $_Table = 'Sessions';
	
	public function __construct()
	{
		parent::__construct();
		
		$this->DeleteExpired();
	}
	
	public function LoadSingle()
	{
		$Site = $this->Single('Base/Structure/Site');
		$Name = $Site && $Site->Get('SessionName') ? $Site->Get('SessionName') : 'SessionID';
		$Timeout = $Site && $Site->Get('SessionTimeout') ? $Site->Get('SessionTimeout') : 86400;
		
		if ( !isset($_COOKIE[$Name]) || !$_COOKIE[$Name] || !$this->Load($_COOKIE[$Name], 'Key') )
		{
			$Key = md5($_SERVER['REMOTE_ADDR'].microtime());
			
			setcookie($Name, $Key, mktime(0, 0, 0, 1, 1, 2030), '/');
			
			$this->Set('Key', $Key);
		}
		
		$this->Ready(false)->Set('ExpireTime', time()+$Timeout)->Save(false);
		
		return $this;
	}
	
	public function Loaded()
	{
		$Data = $this->Get('Data');
		
		if ( is_string($Data) )
		{
			$this->Remove('Data');
			$this->MultiSet(json_decode($Data, true));
		}
	}
	
	public function BeforeSave()
	{
		$Data = $this->Get();
		
		$this->Clear();
		$this->Set($Data['ID'] ? 'UpdateTime' : 'CreateTime', time());
		
		if ( $Data['ID'] ) $this->Set('ID', $Data['ID']);
		if ( $Data['Key'] ) $this->Set('Key', $Data['Key']);
		if ( $Data['CreateTime'] ) $this->Set('CreateTime', $Data['CreateTime']);
		if ( $Data['ExpireTime'] ) $this->Set('ExpireTime', $Data['ExpireTime']);
		
		unset($Data['ID'], $Data['Key'], $Data['CreateTime'], $Data['UpdateTime'], $Data['ExpireTime'], $Data['Data']);
		
		$this->Set('Data', $Data);
	}
	
	public function Save( $Hint = true, $Force = false )
	{
		if ( !$Force ) return false;
		
		$Return = parent::Save($Hint);
		
		$Data = $this->Get('Data');
		
		$this->Remove('Data');
		$this->MultiSet($Data);
		
		return $Return;
	}
		
	public function DeleteExpired()
	{
		$Data = $this->NewModel('Base/Core/Data');
		$Data->Type('DELETE');
		$Data->Tables(array($this->Table()));
		$Data->Filter(array('ExpireTime' => array('lte' => time())));
		$Data->Load();
	}
}

?>