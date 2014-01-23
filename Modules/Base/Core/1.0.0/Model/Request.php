<?php

class Base_Core_Model_Request extends Base_Core_Model
{
	public function SetUrl( $Url )
	{
		$Url = reset(explode('?', $Url));
		$ShortBase = System::Url();
		$LongBase = $ShortBase.System::Get('ScriptName');
		
		if ( strpos($Url, $LongBase) === 0 ) $this->Set('Base', $LongBase.'/');
		elseif ( strpos($Url, $ShortBase) === 0 ) $this->Set('Base', $ShortBase);
		else
		{
			$Url = $ShortBase.$Url;
			
			$this->Set('Base', $ShortBase);
		}
		
		$this->Set('UnresolvedParts', explode('/', ltrim(substr($Url, strlen($this->Get('Base'))), '/')));
		
		if ( !$this->Get('OriginalUrl') ) $this->Set('OriginalUrl', $Url);
		
		return $Url;
	}
	
	public function GetAbsoluteUrl()
	{
		return System::Url().$this->Get('Url');
	}
	
	public function Assign( $Url )
	{
		$UrlParts = explode('/', trim($Url, '/'));
		
		if ( $UrlParts == array_slice($this->Get('UnresolvedParts'), 0, count($UrlParts)) ) $this->Set('UnresolvedParts', array_slice($this->Get('UnresolvedParts'), count($UrlParts)));
	}
}

?>