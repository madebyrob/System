<?php

class Base_Core_View_Setup extends Base_Core_View
{
	public function Process()
	{
		System::Set('DebugRemotes', array($_SERVER['REMOTE_ADDR']));
		
		# set output type
		$this->Type('html');
		
		# load available localizations
		$Localizations = $this->NewCollection('Base/Core/Localization');
		$Localizations->Set('Module', $this->ModuleGroup().'/'.$this->Module());
		$Localizations->Load();
		
		# check selected localization
		if ( !isset($_POST['Localization']) || !($Localization = $Localizations->Item($_POST['Localization'])) )
		{
			# prepare accepted languages
			preg_match_all('~[a-z_]{2,}~i', str_replace('-', '_', strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])), $Matches);
			
			# find localization
			foreach ( $Matches[0] as $Key )
			{
				foreach ( $Localizations->Items() as $Localization )
				{
					if ( strtolower($Localization->Get('Key')) == $Key ) break 2;
				}
			}
		}
		
		$Localization->Set('Selected', 1);
		
		# set default localization
		if ( !$Localization ) $Localization = $Localizations->Item('en_US');
		
		# create vitrual site
		$Site = $this->NewModel('Base/Structure/Site');
		$Site->Set('Localization', $Localization->Get('Key'));
		
		# select design
		$Design = $this->NewModel('Base/Core/Design')->Load('BackendT4M', 1);
		
		# add to singles cache
		System::Set('Singles', md5('Base/Structure/Site'), $Site);
		System::Set('Singles', md5('Base/Core/Design'), $Design);
		System::Set('Singles', md5('Base/Access/Session'), false);
		
		$this->MultiSet($Design->Get(), '_Design');
		$this->MultiSet($Localization->Get(), '_Localization');
		
		$this->Set('Step', 1);
		$this->Set('Localizations', $Localizations->ToArray());
		
		# setup steps
		if ( !isset($_POST['Localization']) || !isset($_POST['Step']) ) return true;
		
		switch ( $_POST['Step'] )
		{
			case 1:
				
				$Result = $this->Check();
				
				if ( count($Result['Errors']) || count($Result['Warnings']) )
				{
					$this->Set('Step', 2);
					$this->Set('Errors', $Result['Errors']);
					$this->Set('Warnings', $Result['Warnings']);
					
					break;
				}
			
			case 2:
			case 3:
				
				$Continue = false;
				
				if ( isset($_POST['Info']) )
				{
					$Errors = array();
					$Data = $_POST['Info'];
					
					foreach ( $Data as $Key => $Value )
					{
						if ( $Key != 'DatabaseTablePrefix' && $Value == '' ) $Errors[$Key] = 1;
					}
					
					if ( $Data['Password'] && $Data['PasswordConfirm'] && $Data['Password'] != $Data['PasswordConfirm'] ) $Errors['PasswordsNotEqual'] = 1;

					if ( $Data['DatabaseHost'] && $Data['DatabasePort'] && $Data['DatabaseName'] && $Data['DatabaseUser'] && $Data['DatabasePassword'] )
					{
						System::Set('DatabaseHost', $Data['DatabaseHost']);
						System::Set('DatabasePort', $Data['DatabasePort']);
						System::Set('DatabaseName', $Data['DatabaseName']);
						System::Set('DatabaseUser', $Data['DatabaseUser']);
						System::Set('DatabasePassword', $Data['DatabasePassword']);
						System::Set('DatabaseTablePrefix', $Data['DatabaseTablePrefix']);
						
						$DatabaseModel = $this->NewModel('Base/Core/Data');
						
						if ( !@$DatabaseModel->Connect() ) $Errors['DatabaseConnection'] = 1;
					}
					
					$this->MultiSet($Data);
					
					if ( $Errors ) $this->MultiSet($Errors, 'Error');
					else
					{
						$this->ResetSingle('Base/Access/Session');
						
						$Path = System::Resolve($this->Selector()).'/';
						$Directory = opendir($Path);
						$Files = array();
												
						# copy directory structure
						$this->CopyContents($Path.'Client', '.');
						
						# search for sql files
						while ( $Name = readdir($Directory) )
						{
							if ( is_file($Path.$Name) && preg_match('~\.sql$~i', $Name) ) $Files[] = $Path.$Name;
						}
						
						# sort sql files
						natsort($Files);
						
						# import sql files
						foreach ( $Files as $File )
						{
							$Content = file_get_contents($File);
							
							foreach ( $Data as $Key => $Value ) $Content = str_replace('__'.$Key.'__', $Value, $Content);
							
							foreach ( explode(';', $Content) as $Query ) $DatabaseModel->Set('Query', $Query)->Load();
						}
						
						# create groups
						$GroupAdmin = $this->NewModel('Base/Access/Group')->Set('Key', 'Administrators')->Set('Status', 1)->Save(false);
						$GroupMain = $this->NewModel('Base/Access/Group')->Set('Key', 'Default')->Set('Status', 1)->Save(false);
						
						# create user
						$User = $this->NewModel('Base/Access/User')->Set('Key', 'admin')->Set('Name', 'Admin')->Set('Password', $Data['Password'])->Set('Groups', $GroupAdmin->Get('ID'))->Set('Status', 1)->Save(false);
						
						# create sites
						$SiteAdmin = $this->NewModel('Base/Structure/Site')->Set('Key', 'admin')->Set('Name', '{Base/Core/SiteAdministration}')->Set('Status', 1)->Save(false);
						$SiteMain = $this->NewModel('Base/Structure/Site')->Set('Key', $Localization->Get('Key'))->Set('Name', $Localization->Get('Name'))->Set('Localization', $Localization->Get('Key'))->Set('Status', 3)->Save(false);

						System::Set('Singles', md5('Base/Structure/Site'), $SiteAdmin);
						
						# create categories
						$CategoryAdmin = $this->NewModel('Base/Structure/Category')->Set('Key', '')->Set('Name', '{Base/Core/CategoryRoot}')->Set('Parent', $SiteAdmin->Get('ID'))->Set('Status', 3)->Save(false);
						$CategoryContent = $this->NewModel('Base/Structure/Category')->Set('Key', 'content')->Set('Name', '{Base/Core/CategoryContent}')->Set('Parent', $CategoryAdmin->Get('ID'))->Set('Permission', $GroupAdmin->Get('ID'))->Set('Status', 2)->Save(false);
						$CategoryFiles = $this->NewModel('Base/Structure/Category')->Set('Key', 'files')->Set('Name', '{Base/Core/CategoryFiles}')->Set('Parent', $CategoryAdmin->Get('ID'))->Set('Permission', $GroupAdmin->Get('ID'))->Set('Status', 2)->Save(false);
						$CategoryAccess = $this->NewModel('Base/Structure/Category')->Set('Key', 'access')->Set('Name', '{Base/Core/CategoryAccess}')->Set('Parent', $CategoryAdmin->Get('ID'))->Set('Permission', $GroupAdmin->Get('ID'))->Set('Status', 2)->Save(false);
						$CategoryMain = $this->NewModel('Base/Structure/Category')->Set('Key', '')->Set('Name', '{Base/Core/CategoryRoot}')->Set('Parent', $SiteMain->Get('ID'))->Set('Status', 3)->Save(false);
						
						# create articles
						$ArticleDashboard = $this->NewModel('Base/Structure/Article')->Set('Key', 'dashboard')->Set('Name', '{Base/Core/ArticleDashboard}')->Set('Category', $CategoryAdmin->Get('ID'))->Set('Layout', 'Main')->Set('ContentType', 'html')->Set('Status', 3)->Save(false);
						$ArticleArticles = $this->NewModel('Base/Structure/Article')->Set('Key', 'articles')->Set('Name', '{Base/Core/ArticleArticles}')->Set('Category', $CategoryContent->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 10)->Set('Status', 2)->Save(false);
						$ArticleCategories = $this->NewModel('Base/Structure/Article')->Set('Key', 'categories')->Set('Name', '{Base/Core/ArticleCategories}')->Set('Category', $CategoryContent->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 20)->Set('Status', 2)->Save(false);
						$ArticleSites = $this->NewModel('Base/Structure/Article')->Set('Key', 'sites')->Set('Name', '{Base/Core/ArticleSites}')->Set('Category', $CategoryContent->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 30)->Set('Status', 2)->Save(false);
						$ArticleAttributes = $this->NewModel('Base/Structure/Article')->Set('Key', 'attributes')->Set('Name', '{Base/Core/ArticleAttributes}')->Set('Category', $CategoryContent->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 40)->Set('Status', 2)->Save(false);
						$ArticleFiles = $this->NewModel('Base/Structure/Article')->Set('Key', 'files')->Set('Name', '{Base/Core/ArticleFiles}')->Set('Category', $CategoryFiles->Get('ID'))->Set('ContentType', 'html')->Set('Status', 3)->Save(false);
						$ArticleUsers = $this->NewModel('Base/Structure/Article')->Set('Key', 'users')->Set('Name', '{Base/Core/ArticleUsers}')->Set('Category', $CategoryAccess->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 10)->Set('Status', 2)->Save(false);
						$ArticleGroups = $this->NewModel('Base/Structure/Article')->Set('Key', 'groups')->Set('Name', '{Base/Core/ArticleGroups}')->Set('Category', $CategoryAccess->Get('ID'))->Set('ContentType', 'html')->Set('SortKey', 20)->Set('Status', 2)->Save(false);
						$ArticleMain = $this->NewModel('Base/Structure/Article')->Set('Key', 'home')->Set('Name', $this->Translate('Home'))->Set('Category', $CategoryMain->Get('ID'))->Set('ContentType', 'html')->Set('Status', 3)->Save(false);
						
						# create instances
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditArticles')->Set('View', 'T4M/Structure/EditArticles')->Set('Article', $ArticleArticles->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditCategories')->Set('View', 'T4M/Structure/EditCategories')->Set('Article', $ArticleCategories->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditSites')->Set('View', 'T4M/Structure/EditSites')->Set('Article', $ArticleSites->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditAttributes')->Set('View', 'T4M/Core/EditAttributes')->Set('Article', $ArticleAttributes->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditFiles')->Set('View', 'T4M/Files/EditFiles')->Set('Article', $ArticleFiles->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditUsers')->Set('View', 'T4M/Access/EditUsers')->Set('Article', $ArticleUsers->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						$this->NewModel('Base/Structure/Instance')->Set('Key', 'EditGroups')->Set('View', 'T4M/Access/EditGroups')->Set('Article', $ArticleGroups->Get('ID'))->Set('Container', 'Content')->Set('Status', 1)->Save(false);
						
						# create attributes
						$AttributeTitlePrefix = $this->NewModel('Base/Core/Attribute')->Set('Key', 'TitlePrefix')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeTitleSuffix = $this->NewModel('Base/Core/Attribute')->Set('Key', 'TitleSuffix')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeUrlNotFound = $this->NewModel('Base/Core/Attribute')->Set('Key', 'UrlNotFound')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeUrlLogin = $this->NewModel('Base/Core/Attribute')->Set('Key', 'UrlLogin')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeUrlLogout = $this->NewModel('Base/Core/Attribute')->Set('Key', 'UrlLogout')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeUrlNewPassword = $this->NewModel('Base/Core/Attribute')->Set('Key', 'UrlNewPassword')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeLoginKey = $this->NewModel('Base/Core/Attribute')->Set('Key', 'LoginKey')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeLocalizations = $this->NewModel('Base/Core/Attribute')->Set('Key', 'Localizations')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeDesign = $this->NewModel('Base/Core/Attribute')->Set('Key', 'Design')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeSessionName = $this->NewModel('Base/Core/Attribute')->Set('Key', 'SessionName')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeMailArticlePassword = $this->NewModel('Base/Core/Attribute')->Set('Key', 'MailArticlePassword')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeProviderMailSender = $this->NewModel('Base/Core/Attribute')->Set('Key', 'ProviderMailSender')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeProviderMailSupport = $this->NewModel('Base/Core/Attribute')->Set('Key', 'ProviderMailSupport')->Set('Model', 'Base/Structure/Site')->Set('Type', 'Value')->Save(false);
						$AttributeHeadline = $this->NewModel('Base/Core/Attribute')->Set('Key', 'Headline')->Set('Model', 'Base/Structure/Article')->Set('Type', 'Text')->Save(false);
						$AttributeContent = $this->NewModel('Base/Core/Attribute')->Set('Key', 'Content')->Set('Model', 'Base/Structure/Article')->Set('Type', 'Html')->Save(false);
						
						# create attribue values
						$SiteAdmin->AddAttribute($AttributeLoginKey->NewValue()->Set('Value', 'Key'));
						$SiteAdmin->AddAttribute($AttributeLocalizations->NewValue()->Set('Value', 'en_US,de_DE'));
						$SiteAdmin->AddAttribute($AttributeDesign->NewValue()->Set('Value', 'BackendT4M'));
						$SiteAdmin->AddAttribute($AttributeSessionName->NewValue()->Set('Value', 'Backend'));
						$SiteAdmin->Save(false);
						
						$SiteMain->AddAttribute($AttributeLoginKey->NewValue()->Set('Value', 'Key'));
						$SiteMain->AddAttribute($AttributeSessionName->NewValue()->Set('Value', 'Frontend'));
						$SiteMain->Save(false);
						
						$ArticleMain->AddAttribute($AttributeHeadline->NewValue()->Set('Value', 'Wilkommen'));
						$ArticleMain->AddAttribute($AttributeContent->NewValue()->Set('Value', '<p>Der erste Inhalt!</p>'));
						$ArticleMain->Save(false);
						
						# create config file
						$Config = array(
							'DatabaseHost' => $Data['DatabaseHost'],
							'DatabasePort' => $Data['DatabasePort'],
							'DatabaseUser' => $Data['DatabaseUser'],
							'DatabasePassword' => $Data['DatabasePassword'],
							'DatabaseName' => $Data['DatabaseName'],
							'DatabaseTablePrefix' => $Data['DatabaseTablePrefix'],
							'DebugRemotes' => ''
						);
						
						$ConfigContent = '';
						
						foreach ( $Config as $Key => $Value ) $ConfigContent .= 'System::Set(\''.$Key.'\', \''.$Value.'\');'.chr(10);
						
						file_put_contents('config.php', '<?php'.chr(10).chr(10).$ConfigContent.chr(10).'?>');
						
						# admin login
						$this->ResetSingle('Base/Access/Session');
						
						$this->NewController('Base/Access/User')->Login(base64_encode($User->Get($SiteAdmin->Get('LoginKey'))), base64_encode($User->Get('Password')));
						
						$Continue = true;
					}
				}
				
				if ( !$Continue )
				{
					$this->Set('Step', 3);
					
					break;
				}
			
			case 4:
				
				$this->Set('Step', 4);
		}
	}
	
	public function Check()
	{
		$Result = array('Errors' => array(), 'Warnings' => array());
		
		if ( preg_match('~^[0-4]\.~', phpversion()) ) $Result['Errors'][] = sprintf($this->Translate('PhpVersionIncompatible'), phpversion());
		
		return $Result;
	}
	
	public function CopyContents( $From, $To )
	{
		if ( !is_dir($From) || $From == '.' || $From == '..' ) return false;
		
		if ( !is_dir($To) ) mkdir($To);
		
		$Directory = opendir($From);
		
		while ( $Name = readdir($Directory) )
		{
			if ( $Name == '.' || $Name == '..' ) continue;
			
			$NewFrom = rtrim($From, '/').'/'.$Name;
			$NewTo = rtrim($To, '/').'/'.$Name;
			
			if ( !$this->CopyContents($NewFrom, $NewTo) ) copy($NewFrom, $NewTo);
		}
		
		return true;
	}
}

?>