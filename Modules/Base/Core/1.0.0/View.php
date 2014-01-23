<?php

abstract class Base_Core_View extends Base_Core_Object
{
	private $_Template = '';
	
	private $_TemplateExtension = 'tpl';
	
	private $_TemplateDir = 'Templates/';
	
	private $_Type;
	
	private $_Replacers;
	
	private $_Contents;
	
	private $_Instance;
	
	public function Process()
	{
		return $this;
	}
	
	public function Template( $Template = NULL )
	{
		if ( is_string($Template) )
		{
			$this->_Template = $Template;
			
			return $this;
		}
		elseif ( $Template !== NULL ) return false;
		
		return $this->_Template ? $this->_Template : $this->Selector();
	}
	
	public function Reset()
	{
		$this->_Data = array();
		
		return $this;
	}
	
	public function Type( $Type )
	{
		$this->_Type = $Type;
		
		return $this;
	}
	
	public function Render()
	{
		$this->Set('_SystemName', System::Get('Name'));
		$this->Set('_SystemVersion', System::Get('Version'));
		$this->Set('_Protocol', System::Get('Protocol'));
		$this->Set('_HostName', System::Get('HostName'));
		$this->Set('_HostUrl', System::Get('HostUrl'));
		$this->Set('_ScriptName', System::Get('ScriptName'));
		$this->Set('_Now', time());
		$this->Set('_Url', System::Url());
		
		if ( is_array(System::Get('Directories')) )
		{
			foreach ( System::Get('Directories') as $Key => $Value ) $this->Set('_'.$Key.'Url', System::Url($Key));
		}
		
		if ( $Instance = $this->Instance() )
		{
			# add instance data to output
			$this->MultiSet($Instance->Get(), '_Instance');
			
			# set instance template to view
			if ( $Instance->Get('Template') ) $this->Template($Instance->Get('Template'));
		}
		
		$Template = $this->Single('Base/Core/Design')->Get('Key').'/'.$this->_TemplateDir.$this->Template().'.'.$this->_TemplateExtension;
		
		if ( $Source = System::Load($Template, true, false, array(System::Path('Designs'), System::Path('System/Designs'))) )
		{
			$Source = $this->Parse($Source, $this->_Data);
			$Source = $this->ParseInstances($Source);
			$Source = $this->ParseTranslations($Source);
			$Source = $this->Paths($Source);
		}
		else $Source = '';
		
		if ( $this->_Type )
		{
			switch ( $this->_Type )
			{
				case 'txt':
				case 'text':
					header('Content-Type: text/plain; Charset=UTF-8');
					break;
				
				case 'xml':
					header('Content-Type: text/xml; Charset=UTF-8');
					break;
				
				default:
					header('Content-Type: text/html; Charset=UTF-8');
			}
			
			echo $Source;
		}
		else return $Source;
	}
	
	public function Parse( $Source, $Data )
	{	
		if ( !$Data ) return $Source;
		
		while ( preg_match('~(\{\?((\!?)(\w+)(?:(\=|\<|\>|%|\.)([^}]*))?)\}(.*?)\{\/\?\2\})|(\{\@(\w+)\}(.*?)\{\/\@\9\})|(\{\$(\w+)((?:\.\w+)*)\})~s', $Source, $Result, PREG_OFFSET_CAPTURE) )
		{
			if ( $Result[1][0] ) # conditions
			{
				$Condition = false;
				
				if ( isset($Data[$Result[4][0]]) )
				{
					if ( is_array($Data[$Result[4][0]]) ) $Value = count($Data[$Result[4][0]]);
					else $Value = $Data[$Result[4][0]];
					
					if ( $Result[5][0] )
					{
						if ( $Result[5][0] == '.' )
						{
							if ( $Result[6][0] == 'empty' ) $Condition = strlen($Value) ? false : true;
						}
						elseif ( $Result[5][0] == '=' ) $Condition = $Value == $Result[6][0];
						elseif ( $Result[5][0] == '<' ) $Condition = $Value < $Result[6][0];
						elseif ( $Result[5][0] == '>' ) $Condition = $Value > $Result[6][0];
						elseif ( $Result[5][0] == '%' ) $Condition = $Value % $Result[6][0];
					}
					else $Condition = $Value;
				}
				
				if ( $Result[3][0] ) $Condition = !$Condition;
				
				$TempSource = $Condition ? $Result[7][0] : '';
			}
			elseif ( $Result[8][0] ) # arrays
			{
				$TempSource = '';
				
				if ( isset($Data[$Result[9][0]]) && ( is_array($Data[$Result[9][0]]) && $Array = $Data[$Result[9][0]] ) || ( is_numeric($Data[$Result[9][0]]) && $Data[$Result[9][0]] != 0 && $Array = range(1, $Data[$Result[9][0]]) ) )
				{
					$Number = 1;
					
					foreach ( $Array as $Key => $Value )
					{
						if ( !is_array($Value) ) $Value = array('_Value' => $Value);
						else $Value['_Value'] = $Value;
						
						$Value['_Key'] = $Key;
						$Value['_Iterator'] = $Number-1;
						$Value['_Number'] = $Number;
						$Value['_First'] = $Number == 1 ? 1 : 0;
						$Value['_Last'] = $Number == count($Data[$Result[9][0]]) ? 1 : 0;
						$Value['_Even'] = $Number % 2 ? 0 : 1;
						$Value['_Odd'] = $Number % 2 ? 1 : 0;
			
						$TempSource .= $this->Parse($Result[10][0], $Value);
						
						++$Number;
					}
				}
			}
			elseif ( $Result[11][0] ) #vars
			{
				$TempSource = '';
				$Modifiers = explode('.', substr($Result[13][0], 1));
				
				if ( $Result[12][0] == '_Output' )
				{
					$TempSource = $this->Modify($Data, $Modifiers);
					
					if ( !is_scalar($TempSource) ) $TempSource = 'ARRAY';
				}
				elseif ( isset($Data[$Result[12][0]]) )
				{
					if ( is_scalar($Data[$Result[12][0]]) )
					{
						$TempSource = $this->Modify(str_replace('{$', '{$$', $Data[$Result[12][0]]), $Modifiers);
						
						if ( !in_array('HTML', $Modifiers) ) $TempSource = htmlspecialchars($TempSource);
					}
					elseif ( is_array($Data[$Result[12][0]]) )
					{
						$TempSource = $this->Modify($Data[$Result[12][0]], $Modifiers);
						
						if ( !is_scalar($TempSource) ) $TempSource = 'ARRAY';
					}
				}
			}
			
			$Source = substr_replace($Source, $TempSource, $Result[0][1], strlen($Result[0][0]));
		}
		
		$Source = preg_replace('~\{(?:\$|\?\!?|\/\?\!?|\@|\/\@)\.+~eU', 'substr(\'\0\', 0, -1)', $Source);
		
		return preg_replace('~>(\s*\r\n)+(\s*)<~s', '>'.chr(13).chr(10).'\2<', str_replace('{$$', '{$', $Source));
	}
	
	public function ParseInstances( $Source )
	{
		preg_match_all('~\{#(\d+)\}~', $Source, $Matches, PREG_SET_ORDER);
		
		if ( $Matches[0] )
		{
			foreach ( $Matches as $Match )
			{
				if ( !$Instance = $this->NewModel('Base/Structure/Instance')->Load($Match[1]) ) continue;
				
				$View = $Instance->View();
				$View->Process();
				
				$Source = str_replace($Match[0], $View->Render(), $Source);
			}
		}
		
		return $Source;
	}
	
	public function ParseTranslations( $Source )
	{
		if ( preg_match_all('~\{(?:(\w+/\w+)/)?(\w+)\}~', $Source, $Matches, PREG_SET_ORDER) )
		{
			foreach ( $Matches as $Match ) $Source = str_replace($Match[0], $this->Translate($Match[2], $Match[1]), $Source);
		}
		
		return $Source;
	}
	
	public function Modify( $Output, $Methods )
	{	
		if ( !$Methods[0] ) return $Output;
		
		if ( is_array($Output) )
		{
			foreach ( $Methods as $Method )
			{
				if ( $Method == 'Count' ) $Output = count($Output);
				elseif ( $Method == 'Json' ) $Output = json_encode($Output);
				elseif ( $Method == 'Printr' ) $Output = print_r($Output, true);
			}
		}
		elseif ( is_string($Output) )
		{
			foreach ( $Methods as $Method )
			{
				if ( $Method == 'Count' || $Method == 'Length' ) $Output = strlen($Output);
				elseif ( $Method == 'DateTimeShort' ) $Output = strftime($this->Translate('DateTimeFormatShort', 'Base/Core'), $Output);
				elseif ( $Method == 'DateTime' ) $Output = strftime($this->Translate('DateTimeFormat', 'Base/Core'), $Output);
				elseif ( $Method == 'DateShort' ) $Output = strftime($this->Translate('DateFormatShort', 'Base/Core'), $Output);
				elseif ( $Method == 'Date' ) $Output = strftime($this->Translate('DateFormat', 'Base/Core'), $Output);
				elseif ( $Method == 'TimeShort' ) $Output = strftime($this->Translate('TimeFormatShort', 'Base/Core'), $Output);
				elseif ( $Method == 'Time' ) $Output = strftime($this->Translate('TimeFormat', 'Base/Core'), $Output);
				elseif ( $Method == 'TimestampYear' ) $Output = strftime('%Y', $Output);
				elseif ( $Method == 'TimestampMonth' ) $Output = strftime('%m', $Output);
				elseif ( $Method == 'TimestampDay' ) $Output = strftime('%d', $Output);
				elseif ( $Method == 'TimestampHour' ) $Output = strftime('%H', $Output);
				elseif ( $Method == 'TimestampMinute' ) $Output = strftime('%M', $Output);
				elseif ( $Method == 'TimestampSecond' ) $Output = strftime('%S', $Output);
				elseif ( $Method == 'TimestampWeek' ) $Output = strftime('%V', $Output);
				elseif ( $Method == 'TimestampWeekDay' ) $Output = strftime('%u', $Output);
				elseif ( $Method == 'TimestampDayName' ) $Output = strftime('{Day%u}', $Output);
				elseif ( $Method == 'TimestampMonthName' ) $Output = strftime('{Month%m}', $Output);
				elseif ( $Method == 'UserName' ) $Output = System::Get('Users', $Output, 'Name');
				elseif ( $Method == 'LowerCase' ) $Output = strtolower($Output);
				elseif ( $Method == 'UpperCase' ) $Output = strtoupper($Output);
				elseif ( $Method == 'CamelCase' ) $Output = preg_replace(array('~(?:^|\W)([a-z])~ie', '~\W~'), array('strtoupper("$1")', ''), $Output);
				elseif ( $Method == 'UpperCaseFirst' ) $Output = ucfirst($Output);
				elseif ( $Method == 'UpperCaseWords' ) $Output = ucwords($Output);
				elseif ( $Method == 'StripTags' ) $Output = strip_tags($Output);
				elseif ( $Method == 'StripBreaks' ) $Output = str_replace(chr(10), ' ', $Output);
				elseif ( $Method == 'EncodeEntities' ) $Output = htmlentities($Output, ENT_QUOTES, 'UTF-8');
				elseif ( $Method == 'DecodeEntities' ) $Output = html_entity_decode($Output, ENT_QUOTES, 'UTF-8');
				elseif ( $Method == 'EncodeURL' ) $Output = urlencode($Output);
				elseif ( $Method == 'DecodeURL' ) $Output = urldecode($Output);
#				elseif ( $Method == 'HTML' ) $Output = $this->Paths($Output);
				elseif ( $Method == 'NL2BR' ) $Output = nl2br($Output);
				elseif ( $Method == 'Brakes2Comma' ) $Output = str_replace(chr(10), ', ', $Output);
				elseif ( $Method == 'Currency' )
				{
					settype($Output, 'float');
					
					$Output = '€ '.number_format($Output, 2, ',', '.');
				}
				elseif ( $Method == 'Percent' )
				{
					settype($Output, 'float');
					
					$Output = $Output*100;
				}
				elseif ( preg_match('~^Cut(\d+)$~', $Method, $Parameters) && strlen(strip_tags($Output)) > $Parameters[1] ) $Output = substr(strip_tags($Output), 0, $Parameters[1]);
				elseif ( preg_match('~^WordCut(\d+)$~', $Method, $Parameters) && strlen(strip_tags($Output)) > $Parameters[1] && $Length = strpos(strip_tags($Output), ' ', $Parameters[1]) ) $Output = substr(strip_tags($Output), 0, $Length);
				elseif ( $Method == 'MaskPaths' ) $Output = preg_replace('~(href|src|data|action|name="src" value)="~i', '\1="::', $Output);
			}
		}
		
		return $Output;
	}
	
	public function Filter()
	{	
		$Output = $this->Source;
		
		if ( $System['OutputFilters'] && !$System['SystemMode'] )
		{
			foreach ( explode(',', $System['OutputFilters']) as $Filter )
			{		
				if ( @!include $System['Dir']['Filters'].'fil.'.trim($Filter).'.php' ) WriteErrorFile('Filter "'.trim($Filter).'" not found.');	
			}
		}
		
		$this->Source = $Output;
	}
	
	public function Paths( $Content )
	{
		$Site = $this->Single('Base/Structure/Site');
		
		return preg_replace('~(href|src|data|action|name="src" value)="([^/#"\{][^:"]*)"~ie', '"\1=\"".(stripos("\2", System::Get("Directories", "Files")) === 0 || !$Site ? System::Url() : $Site->Get("Url"))."\2\""', $Content);
	}
	
	public function SetReplacer( $Data, $Prefix = '', $Suffix = '' )
	{
		foreach ( $Data as $Key => $Value )
		{
			$this->_Replacers['Pattern'][] = $Prefix.$Key.$Suffix;
			$this->_Replacers['Replacer'][] = $Value;
		}
	}
	
	public function Action( $Selector = '' )
	{
		return $this->Single('Base/Structure/Article')->Get('Url').'?Action='.($this->Instance() ? $this->Instance()->Get('ID') : '').'/'.($Selector ? $Selector : $this->Selector()).'/';
	}
	
	public function Contents()
	{
		return array();
	}
	
	public function Content( $Key )
	{
		return $this->_Contents[$Key];
	}
	
	public function HasContent( $Key )
	{
		return isset($this->_Contents[$Key]);
	}
	
	public function AddContent( $Key, $Value )
	{
		$this->_Contents[$Key] = $Value;
			
		return $this;
	}
	
	public function RemoveContent( $Key )
	{
		if ( isset($this->_Contents[$Key]) ) unset($this->_Contents[$Key]);
			
		return $this;
	}
	
	public function ClearContents()
	{
		$this->_Contents = array();
			
		return $this;
	}
	
	public function Instance( $Instance = NULL )
	{
		if ( $Instance )
		{
			$this->_Instance = $Instance;
			$this->ClearContents();
			
			if ( $Instance->Contents() )
			{
				# walk through instance contents
				foreach ( $Instance->Contents()->Items() as $Content )
				{
					# add instance content to view
					$this->AddContent($Content->Get('Key'), $Content->Get('Value'));
				}
			}
			
			return $this;
		}
			
		return $this->_Instance;
	}
}

?>