<?php

class Base_Statistics_Model_Main extends Base_Core_Model
{
	protected $_Table = 'Statistics';
	
	public function Record()
	{
		return false;
		
		if ( System::Get('BackendSite') == $this->Single('Base/Structure/Site')->Get('Key') ) return false;
		
		$this->Set('IP', $_SERVER['REMOTE_ADDR']);
		$this->Set('HostName', gethostbyaddr($_SERVER['REMOTE_ADDR']));
		$this->Set('Agent', $_SERVER['HTTP_USER_AGENT']);
		$this->Set('Category', $this->Single('Base/Structure/Category')->Get('ID'));
		$this->Set('Article', $this->Single('Base/Structure/Article')->Get('ID'));
		$this->Set('Site', $this->Single('Base/Structure/Site')->Get('ID'));
		$this->Set('Language', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$this->Set('Referer', $_SERVER['HTTP_REFERER']);
		$this->Set('RequestURI', $_SERVER['REQUEST_URI']);
		$this->Set('Session', session_id());
		
		$this->Save(false);
		
		return true;
	}
}

?>