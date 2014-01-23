<?php

class Base_Structure_Model_Site extends Base_Core_Model
{
	protected $_Table = 'Sites';
	protected $_UseAttributes = true;
	
	function LoadSingle()
	{
		$Request = $this->Single('Base/Core/Request');
		$Filter = array('Status' => 3);
		
		# build collection filter
		if ( $Key = reset($Request->Get('UnresolvedParts')) )
		{
			# add first request part to filter
			$Filter[] = 'or';
			$Filter['Key'] = $Key;
		}
		
		# load collection
		$Sites = $this->NewCollection();
		$Sites->Set('Filter', $Filter);
		$Sites->Set('OrderBy', array('Status' => 'asc'));
		$Sites->Set('Attributes', true);
		$Sites->Load();
		
		# exit if no sites found
		if ( !$Sites->Count() )
		{
			trigger_error('No default site defined!', E_USER_ERROR);
			
			exit();
		}
		
		# use first site found
		$Site = $Sites->First();
		
		# check if key was found
		if ( $Filter['Key'] && $Filter['Key'] == $Site->Get('Key') )
		{
			# reload if no / at the end of the request url
			if ( count($RequestParts) == 1 )
			{
				header('HTTP/1.1 301 Moved Permanently'); 
				header('Location: '.$Site->Get('Url')); 
				
				exit();
			}
			else $Request->Assign($Site->Get('Key'));
		}
		
		# autoselect localization if not available
		if ( !$Site->Get('Localization') )
		{
			if ( $Site->Get('Localizations') )
			{
				$Localizations = explode(',', $Site->Get('Localizations'));
				
				if ( isset($_COOKIE['SystemLocalization']) && in_array($_COOKIE['SystemLocalization'], $Localizations) ) $Site->Set('Localization', $_COOKIE['SystemLocalization']);
				else
				{
					foreach ( explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $Language )
					{
						$Language = explode(';', $Language);
						
						foreach ( $Localizations as $Localization )
						{
							if ( strtolower($Localization) == strtolower(preg_replace('~\W~', '_', $Language[0])) )
							{
								$Site->Set('Localization', $Localization);
								
								break 2;
							}
						}
					}
				}
			}
			
			# set localization to en_UK if none could be selected
			if ( !$Site->Get('Localization') ) $Site->Set('Localization', 'en_UK');
		}
		
		#setlocale(LC_ALL, $System['Localization']['Ident'], $System['Localization']['Language'], $System['Localization']['Country']);
		#setlocale(LC_NUMERIC, '');
		
		return $Site;
	}
	
	public function Loaded()
	{
		$this->Set('Url', $this->Single('Base/Core/Request')->Get('Base').( $this->Get('Status') == 3 ? '' : $this->Get('Key').'/' ));
		$this->Set('Backend', $this->Get('Key') == System::Get('BackendSite') ? true : false);
	}
	
	public function Delete( $Hint = true )
	{
		$Categories = $this->NewCollection('Base/Structure/Category')->Set('Filter', array('Parent' => $this->Get('ID'), 'Status' => '3'))->Load();
		
		foreach ( $Categories->Items() as $Category ) $Category->Delete(false);
		
		return parent::Delete($Hint);
	}
}

?>