<?php

class T4M_Structure_View_EditCategories extends Base_Core_View
{
	public function Process()
	{
		$Sites = $this->NewCollection('Base/Structure/Site');
		$Sites->Set('Filter', array('Status' => array('lt' => 4)));
		$Sites->Load();
		
		$Site = $_GET['Site'] && $Sites->Item($_GET['Site']) ? $Sites->Item($_GET['Site']) : $Sites->Current();
		
		$Site->Set('Selected', 1);
		
		$this->MultiSet($Site->Get(), 'Site');
		$this->Set('Sites', $Sites->ToArray());
		
		$Categories = $this->NewView('Base/Structure/List');
		$Categories->Template('T4M/Structure/EditCategoriesCategoryList');
		$Categories->AddContent('Site', $Site->Get('ID'));
		$Categories->AddContent('Categories', 'all');
		$Categories->AddContent('StatusCategories', '0,1,2,3');
		$Categories->AddContent('LevelCategories', '0');
		$Categories->AddContent('UnpermittedCategories', 1);
		$Categories->AddContent('StatusArticles', NULL);
		$Categories->AddContent('LevelArticles', 0);
		$Categories->AddContent('UnpermittedArticles', 1);
		$Categories->Process();
		
		if ( count($Categories->Get('Items')) )
		{
			
			$CategoryItems = $Categories->Get('Items');
			$ParentCategories = $Categories->Get('Items');
			
			foreach ( $CategoryItems as $Key => $Value )
			{
				$CategoryItems[$Key]['ActionOpen'] = $this->Single('Base/Structure/Article')->Get('Url').'?Site='.$Site->Get('ID').'&Category='.$Value['ID'];
				$CategoryItems[$Key]['Selected'] = 0;
			}
			
			if ( isset($_GET['Category']) && $CategoryItems['C'.$_GET['Category']] )
			{
				$SelectedCategory = $_GET['Category'];
			}
			else
			{
				$First = reset($CategoryItems);
				$SelectedCategory = $First['ID'];
			}
			
			$CategoryItems['C'.$SelectedCategory]['Selected'] = 1;
			
			$this->Set('CreateCategories', $CategoryItems);
			
			$Categories->Set('Items', $CategoryItems);
			
			$this->Set('Categories', $Categories->Render());
			
			$Category = $this->NewModel('Base/Structure/Category')->TreeItem($SelectedCategory);
			
			$this->MultiSet($Category->Get());
			$this->Set('ActionSave', $this->Action().'Save&Site='.$Site->Get('ID').'&Category='.$SelectedCategory);
			$this->Set('ActionDelete', $this->Action().'Delete/'.$SelectedCategory.'&Site='.$Site->Get('ID'));
			
			if ( $Category->Get('Status') < 3 )
			{
				$ParentCategories['C'.$Category->Get('Parent')]['SelectedParent'] = 1;
				
				foreach ( $ParentCategories as $Key => $Data )
				{
					if ( in_array($Category->Get('ID'), $Data['Path']) ) unset($ParentCategories[$Key]);
				}
				
				$this->Set('ParentCategories', $ParentCategories);
			}
			
			$this->Set('Attributes', $Category->Attributes()->ToArray());
		}
		else
		{
			$this->Set('ActionSave', $this->Action().'Save&Site='.$Site->Get('ID'));
		}
	}
}

?>