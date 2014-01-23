<?php

class Base_Core_View_Panel extends Base_Core_View
{
	public function Process()
	{
		/*
		if ( !$Model = $this->NewModel() ) return false;
		System::Debug();
		$Options = $this->Options;
		
		$SearchTerms = 0;
		
		if ( isset($_GET['SearchSites']) )
		{
			foreach ( $_GET['SearchSites'] as $Key => $Value )
			{
				if ( !strlen($Value) ) continue;
				
				$Options['Where'] .= ' AND `'.$Key.'` LIKE "%'.mysql_real_escape_string($Value).'%"';
				
				++$SearchTerms;
			}
			
			$this->Set('SearchSites', $_GET['SearchSites'], true);
		}
		
		#$this->Options['Debug'] = 1;
		$this->Options['Table'] = $Model->Table;/*
		$this->Data->Load($this->Options);
		
		$Total = $this->Data->Count();
		
		$Limit = $_GET['Limit'] ? $_GET['Limit'] : 10;
		$Page = $_GET['Page'] ? $_GET['Page'] : 1;
		
		$SortTerm = $_GET['SortTerm'] ? $_GET['SortTerm'] : 'ID';
		$SortDir = $_GET['SortDir'] ? $_GET['SortDir'] : 'ASC';
		
		$this->Options['SortTerms'] = '`'.mysql_real_escape_string($SortTerm).'` '.mysql_real_escape_string($SortDir).' LIMIT '.mysql_real_escape_string($Page*$Limit-$Limit).', '.mysql_real_escape_string($Limit);
		
		$this->Options['SortTerms'] = '`ID` ASC LIMIT 0, 1';
		
		$Items = $Model->Load($this->Options);
		*/
		
		if ( !$this->Get('ID') ) $this->Set('ID', 0);
		
		$this->Set('Controller', $this->Action());
		#System::Debug($this->);
		#$this->Data->Get(array('Table' => 'Actions', 'Where' => ));
		
		$this->Set('Actions', json_encode($this->Actions()));
		
		$Items = $this->NewController()->Load();
		
		$this->Set('JsonItems', json_encode($Items->ToArray()));
		$this->Set('Items', $Items->ToArray());
		
		$this->Set('Namespace', $this->Get('ParentNamespace').($this->Get('ID') ? $this->Get('ID') : '').$this->Name());
		$this->Set('FieldTitle', $this->FieldTitle());
		$this->Set('FieldsCreate', json_encode($this->FieldsCreate()));
		$this->Set('FieldsUpdate', json_encode($this->FieldsUpdate()));
		$this->Set('FieldsFilter', json_encode($this->FieldsFilter()));
		#System::Debug($this->FieldsUpdate());
		$this->Set('SearchAllowed', $Sites || $SearchTerms ? 1 : 0);
		$this->Set('SearchTerms', $SearchTerms ? 1 : 0);
		$this->Set('SortTerm', $SortTerm);
		$this->Set('SortDir', $SortDir);
		
		$Actions = $this->NewModel('Base/Core/Data');
		$Actions->Tables(array('Actions'));
		$Actions->Filter(array('View' => $this->NewController()->Model));
		$Actions->OrderBy(array('SortKey' => 'asc'));
		$Actions->Load();
		
		while ( $Action = $Actions->Next() )
		{
			d($Action);
		}
	}
	
	public function Options()
	{
		return array();
	}
	
	public function Actions()
	{
		return array();
	}
	
	public function FieldTitle()
	{
		return 'ID';
	}
	
	public function FieldsCreate()
	{
		return array();
	}
	
	public function FieldsUpdate()
	{
		return array();
	}
	
	public function FieldsFilter()
	{
		return array();
	}
	
	public function Conditions()
	{
		return '1';
	}
	
	public function TranslateFields( $Fields )
	{
		foreach ( $Fields as $Key => &$Value ) $Value['Name'] = $this->Translate('Label'.$Key);
		
		return $Fields;
	}
	
	public function StatusArray( $Prefix = '', $Length = 2 )
	{
		$Statuses = array();
		
		for ( $i = 0; $i < $Length; ++$i )
		{
			$Statuses[] = array(
				'Value' => $i,
				'Name' => $this->Translate($Prefix.'Status'.$i)
			);
		}
		
		return $Statuses;
	}
}

?>