<?php

class Base_Core_Controller_Main extends Base_Core_Controller
{
	function ProcessUrl( $Url = NULL )
	{
		$Request = $this->Single('Base/Core/Request');
		$Request->Set('Url', $Url === NULL ? $_SERVER['REQUEST_URI'] : $Url);
		
		$this->ResetSingle('Base/Structure/Site');
		$this->ResetSingle('Base/Structure/Category');
		$this->ResetSingle('Base/Structure/Article');
		
		# process action
		$this->ProcessAction();
		
		$Site = $this->Single('Base/Structure/Site');
		$Category = $this->Single('Base/Structure/Category');
		$Article = $this->Single('Base/Structure/Article');
		$Session = $this->Single('Base/Access/Session');
		
		# check category and article
		if ( !$Category || !$Article ) $this->NotFound($Url);
		
		# check permission
		if ( !$this->NewModel('Base/Access/Group')->CheckMembership($Article->Get('AutoPermission')) ) $this->Unauthorized($Url);
		
		if ( $Article->Get('Url') != $Site->Get('Url').$Site->Get('UrlNotFound') && $Article->Get('Url') != $Site->Get('Url').$Site->Get('UrlLogin') )
		{
			# check if article has changed
			if ( $Session->Get('RecentUrl') && $Session->Get('RecentUrl') != $Article->Get('Url').($Article->Get('Query') ? '?'.$Article->Get('Query') : '') )
			{
				$Session->Set('LastUrl', $Session->Get('RecentUrl'));
				$Session->Set('LastArticle', $Session->Get('RecentArticle'));
			}
			
			# remember current article for next reload
			$Session->Set('RecentUrl', $Article->Get('Url').($Article->Get('Query') ? '?'.$Article->Get('Query') : ''));
			$Session->Set('RecentArticle', $Article->Get('ID'));
		}
		
		# output
		$View = $this->NewView();
		$View->Process();
		$View->Render();
		
		$this->NewModel('Base/Statistics/Main')->Record();
		
		if ( $Session = $this->Single('Base/Access/Session') ) $Session->Save(false, true);
		
		exit();
	}
	
	public function ProcessAction()
	{
		if ( !isset($_GET['Action']) || !preg_match('~^(\d*)/([a-z0-9]+/[a-z0-9]+/[a-z0-9]+)/([a-z0-9]+)(/.*)?$~i', $_GET['Action'], $Parts) ) return false;
		
		$Result = NULL;
		
		if ( $Controller = $this->NewController($Parts[2]) )
		{
			unset($_GET['Action']);
			
			if ( method_exists($Controller, $Parts[3]) )
			{
				if ( is_numeric($Parts[1]) && $Instance = $this->NewModel('Base/Structure/Instance')->Load($Parts[1]) ) $Controller->Instance($Instance);
				
				$Result = call_user_func_array(array($Controller, $Parts[3]), explode('/', substr($Parts[4], 1)));
				
				if ( $Instance ) System::Set('ActionData', $Instance->Get('ID'), $Controller->Get());
			}
			else $this->Error('UnknownAction', $Parts[2].'/'.$Parts[3]);
		}
		else $this->Error('UnknownController', $Parts[2]);
		
		if ( isset($_GET['Ajax']) ) exit(json_encode(array('Messages' => System::$Messages, 'Result' => $Result, 'Data' => $Controller->Get())));
		
	}
	
	public function NotFound( $Url )
	{
		$Site = $this->Single('Base/Structure/Site');
		
		if ( $Site->Get('UrlNotFound') !== NULL && $Url != $Site->Get('Url').$Site->Get('UrlNotFound') )
		{
			if ( !System::Get('HideNotFoundError') ) $this->Error($this->Single('Base/Structure/Category') ? 'ArticleNotFound' : 'CategoryNotFound', $Url);
			
			header('HTTP/1.0 404 Not Found');
			
			$this->ProcessUrl($Site->Get('Url').$Site->Get('UrlNotFound'));
		}
		else
		{
			trigger_error('"UrlNotFound" for site "'.$Site->Get('Name').'" is invalid!', E_USER_ERROR);
			
			exit();
		}
	}
	
	public function Unauthorized( $Url )
	{
		$Site = $this->Single('Base/Structure/Site');
		
		if ( $Site->Get('UrlLogin') !== NULL && $Url != $Site->Get('Url').$Site->Get('UrlLogin') )
		{
			if ( !System::Get('HideUnauthorizedError') ) $this->Warning('Unauthorized', $Url);
			
			header('HTTP/1.0 401 Unauthorized'); 
			
			$this->ProcessUrl($Site->Get('Url').$Site->Get('UrlLogin'));
		}
		else
		{
			trigger_error('"UrlLogin" for site "'.$Site->Get('Name').'" is invalid!', E_USER_ERROR);
			
			exit();
		}
	}
	
	public function Message()
	{
		$Arguments = func_get_args();
		$Session = $this->Single('Base/Access/Session');
		$Messages = $Session->Get('Messages');
		
		if ( !is_array($Messages) ) $Messages = array();
		
		array_push($Messages, array('Type' => array_shift($Arguments), 'Area' => array_shift($Arguments), 'Message' => vsprintf(array_shift($Arguments), $Arguments)));
		
		$Session->Set('Messages', $Messages)->Save(false);
	}
}

?>