<?php

class Base_Structure_View_List extends Base_Core_View
{
	public function Contents()
	{
		$Contents = array(
			array('Key' => 'Categories', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'StatusCategories', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'LevelCategories', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'UnpermittedCategories', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'StatusArticles', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'LevelArticles', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'UnpermittedArticles', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'SortKey', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'SortType', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'SortDir', 'Type' => 'Value', 'Value' => ''),
			array('Key' => 'Limit', 'Type' => 'Value', 'Value' => '')
		);
		
		return $Contents;
	}
	
	public function Process()
	{
		# attributes
		$ContentSite = $this->Content('Site') ? $this->Content('Site') : $this->Single('Base/Structure/Site')->Get('ID');
		$ContentCategories = $this->Content('Categories');
		
		$ContentStatusCategories = explode(',', $this->Content('StatusCategories'));
		$ContentLevelCategories = is_numeric($this->Content('LevelCategories')) ? $this->Content('LevelCategories') : 0;
		$ContentUnpermittedCategories = $this->Content('UnpermittedCategories');
		
		$ContentStatusArticles = explode(',', $this->Content('StatusArticles'));
		$ContentLevelArticles = is_numeric($this->Content('LevelArticles')) ? $this->Content('LevelArticles') : 0;
		$ContentUnpermittedArticles = $this->Content('UnpermittedArticles');
		
		# compatibility
		if ( $this->HasContent('Levels') ) $ContentLevelCategories = $ContentLevelArticles = is_numeric($this->Content('Levels')) ? $this->Content('Levels') : 0;
		if ( $this->HasContent('ShowUnpermittedItems') ) $ContentUnpermittedCategories = $ContentUnpermittedArticles = $this->Content('ShowUnpermittedItems');
		
		# item collection
		$Items = $this->NewCollection(false);
		
		# categories to load articles from
		$ArticleCategories = array();
		
		# check categories attribute
		if ( !preg_match('~^(all|(?:[1-9]\d*|root|current)(?:,[1-9]\d*|,root|,current)*)$~', $ContentCategories, $Matches) )
		{
			trigger_error('Content "Categories" invalid ('.$ContentCategories.')', E_USER_ERROR);
			
			return false;
		}
		
		$Categories = $this->NewCollection('Base/Structure/Category');
		
		if ( $ContentCategories == 'all' )
		{
			foreach ( $this->NewModel('Base/Structure/Category')->SiteTree($ContentSite)->Items() as $Category )
			{
				$Category->Set('OffsetLevel', $Category->Get('Level'));
				
				$Categories->AddItem($Category, $Category->Get('ID'));
			}
		}
		else
		{
			if ( strstr($ContentCategories, 'root') ) $ContentCategories = str_replace('root', $this->NewModel('Base/Structure/Category')->TreeRoot($ContentSite)->Get('ID'), $ContentCategories);
			if ( strstr($ContentCategories, 'current') ) $ContentCategories = str_replace('current', $this->Single('Base/Structure/Category')->Get('ID'), $ContentCategories);
			
			foreach ( explode(',', $ContentCategories) as $ID )
			{
				if ( !$this->NewModel('Base/Structure/Category')->Tree()->Item($ID) ) continue;
				
				foreach ( $this->NewModel('Base/Structure/Category')->TreePart($ID)->Items() as $Category )
				{
					$Categories->AddItem($Category, $Category->Get('ID'));
				}
				
				$ArticleCategories[] = $ID;
			}
		}
		
		foreach ( $Categories->Items() as $Category )
		{
			if ( $ContentLevelArticles && $Category->Get('OffsetLevel')+1 < $ContentLevelArticles ) $ArticleCategories[] = $Category->Get('ID');
			
			# check level
			if ( $ContentLevelCategories && $Category->Get('OffsetLevel') >= $ContentLevelCategories ) continue;
			
			# check status
			if ( !in_array($Category->Get('Status'), $ContentStatusCategories) ) continue;
			
			# check permission
			if ( !$ContentUnpermittedCategories && !$this->NewModel('Base/Access/Group')->CheckMembership($Category->Get('AutoPermission')) ) continue;
			
			# duplicate category for use in list
			$Category = $Category->Duplicate();
			
			# set selected if category in current path
			$Path = $this->Single('Base/Structure/Category')->Get('Path');
			
			$Category->Set('Selected', $Path[$Category->Get('Level')] == $Category->Get('ID') ? 1 : 0);
			
			# add category to collection
			$Items->AddItem($Category, 'C'.$Category->Get('ID'));
		}
		
		if ( implode('', $ContentStatusArticles) )
		{
			# load articles
			$Articles = $this->NewCollection('Base/Structure/Article');
			$Articles->Set('Filter', array('Category' => array('in' => $ArticleCategories), 'Status' => array('in' => $ContentStatusArticles)));
			$Articles->Set('Attributes', true);
			$Articles->Set('Contents', true);
			$Articles->Load();
			
			# articles to load instances from
			$InstanceArticles = array();
			
			foreach ( $Articles->Items() as $Article )
			{
				$Parent = $Categories->Item($Article->Get('Category'));
				
				$Article->Set('OffsetLevel', $Parent ? $Parent->Get('OffsetLevel')+1 : 0);
				
				#check permission
				if ( !$ContentUnpermittedArticles && !$this->NewModel('Base/Access/Group')->CheckMembership($Article->Get('AutoPermission')) ) continue;
				
				$Article->Set('TreeKey', $this->NewModel('Base/Structure/Category')->Tree()->Item($Article->Get('Category'))->Get('TreeKey').'-'.$Article->Get('SortKey').str_pad($Article->Get('ID'), 10, '0', STR_PAD_LEFT));
				
				# add article data
				$Article->Set('Selected', $this->Single('Base/Structure/Article')->Get('ID') == $Article->Get('ID') ? 1 : 0);
				
				# add article to collection
				$Items->AddItem($Article, 'A'.$Article->Get('ID'));
				
				$InstanceArticles[] = $Article->Get('ID');
			}
			
			# load instances
			$Instances = $this->NewCollection('Base/Structure/Instance');
			$Instances->Set('Filter', array('Article' => array('in' => $InstanceArticles), 'Status' => 1));
			$Instances->Set('Attributes', true);
			$Instances->Load();
			
			foreach ( $Instances->Items() as $Instance )
			{
				foreach ( $Instance->Contents()->Items() as $Content )
				{
					$Items->Item('A'.$Instance->Get('Article'))->Set('InstanceContent'.$Instance->Get('Key').$Content->Get('Key'), $Content->Get('Value'));
				}
			}
		}
		
		if ( $this->Content('SortKey') )
		{
			$Options = array(
				'Key' => $this->Content('SortKey'),
				'Type' => $this->Content('SortType') ? $this->Content('SortType') : 'Nati',
				'Dir' => $this->Content('SortDir') ? $this->Content('SortDir') : 'Asc'
			);
			
			$Items->Sort($Options);
		}
		else
		{
			$Items->Sort(array('Key' => 'TreeKey', 'Type' => 'Nati'));
			
			if ( $Items->Count() )
			{
				$LastKey = NULL;
				$LastLevel = 0;
				
				foreach ( $Items->Items() as $Key => $Item )
				{
					if ( $LastKey === NULL ) $Item->Set('First', 1);
					
					if ( $LastLevel > $Item->Get('OffsetLevel') )
					{
						$Items->Item($LastKey)->Set('Last', 1);
					}
					elseif ( $LastLevel < $Item->Get('OffsetLevel') )
					{
						$Item->Set('First', 1);
						
						if ( $LastKey ) $Items->Item($LastKey)->Set('Submenu', 1);
					}
					
					if ( $LastLevel-$Item->Get('OffsetLevel') > 0 ) $Item->Set('PreviousLevelOffset', $LastLevel-$Item->Get('OffsetLevel'));
					
					$LastKey = $Key;
					$LastLevel = $Item->Get('OffsetLevel');
				}
				
				foreach ( array_reverse($Items->Items()) as $Key => $Item )
				{
					if ( $LastLevel == $Item->Get('OffsetLevel') )
					{
						$Item->Set('Last', 1);
						
						if ( $LastLevel == 0 ) break;
						
						--$LastLevel;
					}
				}
			}
		}
		
		$this->Set('Items', $this->Content('Limit') ? array_slice($Items->ToArray(), 0, $this->Content('Limit')) : $Items->ToArray());
	}
}

?>