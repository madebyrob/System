<?php

class Base_Structure_Model_Category extends Base_Core_Model
{
	protected $_Table = 'Categories';
	protected $_UseAttributes = true;
	
	public function LoadSingle()
	{
		# load complete tree for current site
		$Categories = $this->NewModel()->SiteTree();
		
		# insert categories into global registry
		System::Set('Categories', $Categories);
		
		# search current category by url
		$Site = $this->Single('Base/Structure/Site');
		$Request = $this->Single('Base/Core/Request');
		$Closest = $Categories->First();
		
		foreach ( $Categories->Items() as $Category )
		{
			if ( !$Category->Get('Status') ) continue;
			
			if ( $Request->Get('Url') == $Category->Get('Url') )
			{
				$Closest = $Category;
				
				break;
			}
			
			if ( strstr($Request->Get('Url'), $Category->Get('Url')) && strlen($Category->Get('Url')) > strlen($Closest->Get('Url')) ) $Closest = $Category;
			
			if ( $Category->Get('Url') == $Request->Get('Url').'/' )
			{
				header('HTTP/1.1 301 Moved Permanently'); 
				header('Location: '.$Category->Get('Url')); 
				
				exit();
			}
		}
		
		$Request->Assign(substr($Closest->Get('Url'), strlen($Site->Get('Url'))));
		
		return $Closest;
	}
	
	public function Tree()
	{
		if ( !$Tree = System::Get('Tree') )
		{
			$Sites = $this->NewCollection('Base/Structure/Site')->Load();
			
			# get all categories
			$Tree = $this->NewCollection();
			$Tree->Set('OrderBy', array('Status' => 'DESC'));
			$Tree->Set('Attributes', true);
			$Tree->Load();
			
			# temp category storage
			$Items = $Tree->Items();
			
			reset($Items);
			
			# empty collection + insert root
			$Tree->RemoveAll();
			
			# build tree (depth level = cycles)
			while ( $Items )
			{
				$Category = current($Items);
				
				if ( !$Category->Get('Parent') )
				{
					# remove from raw items list
					unset($Items[$Category->Get('ID')]);
				}
				elseif ( $Category->Get('Status') == 3 )
				{
					$Category->Set('AutoLayout', $Category->Get('Layout'));
					$Category->Set('Url', $Sites->Item($Category->Get('Parent'))->Get('Url'));
					$Category->Set('Path', array($Category->Get('ID')));
					$Category->Set('Level', 0);
					$Category->Set('Subcategories', array());
					$Category->Set('TreeKey', $Sites->Item($Category->Get('Parent'))->Get('ID').'-'.$Category->Get('ID'));
					$Category->Set('AutoLayout', $Category->Get('Layout') ? $Category->Get('Layout') : 'Default');
					$Category->Set('AutoPermission', $Category->Get('Permission') ? $Category->Get('Permission') : '0');
					
					# add category to items list
					$Tree->AddItem($Category, $Category->Get('ID'));#d($Tree->ToArray());exit();
					
					# remove from raw items list
					unset($Items[$Category->Get('ID')]);
				}
				elseif ( $Parent = $Tree->Item($Category->Get('Parent')) )
				{
					# build url
					$Category->Set('Url', $Parent->Get('Url').$Category->Get('Key').'/');
					
					# build path + set level
					$Path = $Parent->Get('Path');
					
					array_push($Path, $Category->Get('ID'));
					
					$Category->Set('Path', $Path);
					$Category->Set('Level', count($Category->Get('Path'))-1);
					
					# inherit from parent
					if ( $Category->Get('Status') > $Parent->Get('Status') ) $Category->Set('Status', $Parent->Get('Status'));
					
					if ( strlen($Category->Get('Permission')) ) $Category->Set('AutoPermission', $Category->Get('Permission'));
					elseif ( strlen($Parent->Get('AutoPermission')) ) $Category->Set('AutoPermission', $Parent->Get('AutoPermission'));
					
					if ( strlen($Category->Get('Layout')) ) $Category->Set('AutoLayout', $Category->Get('Layout'));
					elseif ( strlen($Parent->Get('AutoLayout')) ) $Category->Set('AutoLayout', $Parent->Get('AutoLayout'));
					
					# set subcategories
					$Category->Set('Subcategories', array());
					
					$Subcategories = $Parent->Get('Subcategories');
					
					array_push($Subcategories, $Category->Get('ID'));
					
					$Parent->Set('Subcategories', $Subcategories);
					
					# set unique key for sorting
					$Category->Set('TreeKey', $Parent->Get('TreeKey').'-'.$Category->Get('SortKey').str_pad($Category->Get('ID'), 10, '0', STR_PAD_LEFT));
					
					# add category to items list
					$Tree->AddItem($Category, $Category->Get('ID'));
					
					# remove from raw items list
					unset($Items[$Category->Get('ID')]);
				}
				
				if ( !next($Items) ) reset($Items);
			}
			
			# sort by unique key
			$Tree->Sort(array('Key' => 'TreeKey', 'Type' => 'Nati'));
			
			# add tree to registry
			System::Set('Tree', $Tree);
		}
		
		return $Tree;
	}
	
	public function SiteTree( $Site = 0 )
	{
		if ( !$Site ) $Site = $this->Single('Base/Structure/Site')->Get('ID');
		
		$Categories = $this->NewCollection();
		$Add = false;
		
		foreach ( $this->Tree()->Items() as $Key => $Category )
		{
			if ( $Category->Get('Status') == 3 )
			{
				if ( $Category->Get('Parent') == $Site ) $Add = true;
				elseif ( $Add ) break;
			}
			
			if ( $Add ) $Categories->AddItem($Category, $Key);
		}
		
		return $Categories;
	}
	
	public function TreePart( $Base, $Levels = 0 )
	{
		$Tree = $this->Tree();
		
		if ( $Base = $Tree->Item($Base) )
		{
			$Categories = $this->NewCollection()->Items($Tree->Items());
			
			foreach ( $Categories->Items() as $Key => $Category )
			{
				$OffsetLevel = $Category->Get('Level')-$Base->Get('Level')-1;
				
				if ( $Base->Get('ID') == $Category->Get('ID') || !in_array($Base->Get('ID'), $Category->Get('Path')) || ( $Levels && $OffsetLevel >= $Levels ) ) $Categories->RemoveItem($Key);
				else $Category->Set('OffsetLevel', $OffsetLevel);
			}
			
			return $Categories;
		}
		
		return false;
	}
	
	public function TreeRoot( $Site = 0 )
	{
		if ( !$Site ) $Site = $this->Single('Base/Structure/Site')->Get('ID');
		
		foreach ( $this->Tree()->Items() as $Category )
		{
			if ( $Category->Get('Status') == 3 && $Category->Get('Parent') == $Site ) return $Category;
		}
		
		return NULL;
	}
	
	public function TreeItem( $ID )
	{
		if ( $Item = $this->Tree()->Item($ID) ) return $Item;
		
		return NULL;
	}
	
	public function ResetTree()
	{
		System::Set('Tree', NULL);
		
		return $this;
	}
	
	public function Save( $Hint = true )
	{
		# rebuild tree if category was saved
		if ( $Return = parent::Save($Hint) ) $this->ResetTree();
		
		return $Return;
	}
	
	public function Delete( $Hint = true )
	{
		$Categories = $this->NewCollection()->Set('Filter', array('Parent' => $this->Get('ID'), 'Status' => array('lt' => 3)))->Load();
		
		foreach ( $Categories->Items() as $Category ) $Category->Delete(false);
		
		$Articles = $this->NewCollection('Base/Structure/Article')->Set('Filter', array('Category' => $this->Get('ID')))->Load();
		
		foreach ( $Articles->Items() as $Article ) $Article->Delete(false);
		
		# rebuild tree of site if category was deleted
		if ( ($Return = parent::Delete($Hint)) && System::Get('Trees', $this->Get('Site')) ) System::Set('Trees', $this->Get('Site'), NULL);
		
		return $Return;
	}
	
	public function Articles( $Options = array() )
	{
		return $this->NewCollection('Base/Structure/Article')->MultiSet(array_merge(array('Filter' => array('Category' => $this->Get('ID'))), $Options))->Load();
	}
}

?>