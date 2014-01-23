<?php

class Base_Access_Controller_User extends Base_Core_Controller
{
	# select user by request
	public function Login( $Key = NULL, $Password = NULL, $RedirectTo = NULL )
	{
		$Site = $this->Single('Base/Structure/Site');
		
		if ( $Key && $Password )
		{
			$Key = base64_decode($Key);
			$Password = base64_decode($Password);
		}
		elseif ( $_POST['UserKey'] && $_POST['UserPassword'] )
		{
			$Key = $_POST['UserKey'];
			$Password = $_POST['UserPassword'];
		}
		else return false;
		
		if ( !$User = $this->NewModel()->Load($Key, $Site->Get('LoginKey')) )
		{
			$this->Error('UserNotFound');
			
			return false;
		}
		
		if ( !$User->Get('Status') )
		{
			$this->Error('UserNotActive');
			
			return false;
		}
		
		if ( !$User->Get('Password') || $User->Get('Password') != ( $_POST['UserPassword'] ? crypt($Password, $User->Get('Password')) : $Password ) )
		{
			$this->Error('WrongPassword', $Site->Get('Url').$Site->Get('UrlNewPassword'));
			
			return false;
		}
		
		if ( !$Site->Get('HideHintLoggedIn') ) $this->Hint('LoggedIn');
		
		$User->Set('Status', 2);
		$User->Set('LastActivity', time());
		$User->Save(false);
		
		$this->Single('Base/Access/Session')->Set('User', $User->Get('ID'));
		$this->ResetSingle();
		
		if ( !$RedirectTo && isset($_POST['RedirectTo']) ) $RedirectTo = $_POST['RedirectTo'];
		
		if ( $RedirectTo ) $this->NewController('Base/Core/Main')->ProcessUrl($RedirectTo);
		
		return true;
	}
	
	# unselect user
	public function Logout()
	{
		$this->Single('Base/Access/Session')->Remove('User');
		$this->ResetSingle();
		
		if ( !$this->Single('Base/Structure/Site')->Get('HideHintLoggedOut') ) $this->Hint('LoggedOut');
	}
	
	# send login mail
	public function Password()
	{
		if ( $this->Single()->Get('Status') == 1 && $_POST['EmailAddress'] )
		{
			# check mail
			if ( !preg_match('~^[a-z0-9-._]+@[a-z0-9.-]+\.[a-z]{2,}$~i', $_POST['EmailAddress']) )
			{
				$this->Error('InvalidEmailAddress');
				
				return false;
			}
			
			# get user
			if ( !$User = $this->NewModel()->Load($_POST['EmailAddress'], 'EmailAddress') )
			{
				$this->Error('UnknownEmailAddress');
				
				return false;
			}
			
			# check if user logged in before
			if ( !$User->Get('LastActivity') )
			{
				$this->Error('RecoveryNotPossible');
				
				return false;
			}
			
			$Site = $this->Single('Base/Structure/Site');
			$Options = array(
				'Receiver' => $User->Get('EmailAddress'),
				'Sender' => $Site->Get('ProviderMailSender'),
				'Data' => $User->Get()
			);
			
			$Options['Data']['Link'] = System::Get('HostUrl').$this->Single('Base/Structure/Article')->Get('Url').'?Action='.System::Get('Instance', 'ID').'/'.$this->Selector().'/NewPassword/'.base64_encode($User->Get($Site->Get('LoginKey'))).'/'.base64_encode($User->Get('Password'));
			
			# load article
			if ( !is_numeric($Site->Get('MailArticlePassword')) || !($Article = $this->NewModel('Base/Structure/Article')->Load($Site->Get('MailArticlePassword'))) )
			{
				$this->Error('MailArticleInvalid'); 
				
				return false;
			}
			
			# send article
			if ( !$Article->Send($Options) )
			{
				$this->Error('PasswordSendError'); 
				
				return false;
			}
			
			$this->Hint('PasswordSent');
				
			return true;
		}
		elseif ( $this->Single()->Get('Status') == 2 )
		{
			if ( !$_POST['PasswordOld'] || !$_POST['PasswordNew'] || !$_POST['PasswordConfirm'] ) return false;
			
			if ( $_POST['PasswordOld'] !== $this->Single()->Get('Password') && crypt($_POST['PasswordOld'], $this->Single()->Get('Password')) !== $this->Single()->Get('Password') )
			{
				$this->Error('OldPasswordIncorrect');
				
				return false;
			}
			
			if ( $_POST['PasswordNew'] != $_POST['PasswordConfirm'] )
			{
				$this->Error('NewPasswordsNotEqual');
				
				$this->Set('Password', $this->Single()->Get('Password'));
				
				return false;
			}
			
			if ( $this->Single()->Set('Password', $_POST['PasswordNew'])->Save(false) )
			{
				$this->Hint('NewPasswordSet');
				
				return true;
			}
			
			$this->Error('PasswordError');
			
			return false;
		}
	}
	
	public function NewPassword( $Key = NULL, $Password = NULL  )
	{
		if ( !$Key || !$Password ) return false;
		
		$this->Login($Key, $Password);
		
		$this->Set('Password', base64_decode($Password));
		
		return true;
	}
}

?>