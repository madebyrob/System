<?php

class T4M_Structure_View_EditArticles extends Base_Core_View
{
	public function Process()
	{
		if ( is_numeric($_GET['Article']) )
		{
			$this->Set('BlockArticle', 1);
			$this->Set('ActionSave', $this->Action().'Save&Article='.$_GET['Article']);
			
			$Article = $this->NewModel('Base/Structure/Article')->Load($_GET['Article']);
			
			$this->MultiSet($Article->Get());
			$this->Set('Attributes', $Article->Attributes()->ToArray());
			
			$Instances = $this->NewCollection('Base/Structure/Instance');
			$Instances->Set('Filter', array('Article' => $Article->Get('ID')));
			$Instances->Set('Attributes', true);
			$Instances->Set('OrderBy', array('Container' => 'ASC', 'SortKey' => 'ASC'));
			$Instances->Load();
			
			foreach ( $Instances->Items() as $Instance )
			{
				$Instance->Set('ActionRemove', $this->Action().'RemoveInstance/'.$Instance->Get('ID').'&Article='.$_GET['Article']);
				$Instance->Set('Contents', $Instance->Contents()->ToArray());
			}
			
			$this->Set('Instances', $Instances->ToArray(true));
			
			$Categories = $this->NewModel('Base/Structure/Category')->Tree();
			
			if ( $Category = $Categories->Item($Article->Get('Category')) ) $Category->Set('Selected', 1);
			
			$this->Set('Categories', $Categories->ToArray());
		}
		elseif ( is_numeric($_GET['Category']) )
		{
			$this->Set('BlockArticles', 1);
			
			$Articles = $this->NewCollection('Base/Structure/Article');
			$Articles->Set('Filter', array('Category' => $_GET['Category']));
			$Articles->Set('OrderBy', array('SortKey' => 'ASC', 'Name' => 'ASC'));
			$Articles->Load();
			
			$this->MultiSet($this->NewModel('Base/Structure/Category')->Tree()->Item($_GET['Category'])->Get(), 'Category');
			$this->Set('Articles', $Articles->ToArray());
			$this->Set('ActionSave', $this->Action().'Save&Category='.$_GET['Category']);
			$this->Set('ActionDelete', $this->Action().'Delete&Category='.$_GET['Category']);
			$this->Set('Browser', $_GET['Browser']);
		}
		else
		{
			$this->Set('BlockCategories', 1);
			
			$Sites = $this->NewCollection('Base/Structure/Site');
			$Sites->Set('Filter', array('Status' => array('lt' => 4)));
			$Sites->Load();
			
			$Site = $_GET['Site'] && $Sites->Item($_GET['Site']) ? $Sites->Item($_GET['Site']) : $Sites->Current();
			
			$Site->Set('Selected', 1);
			
			$this->Set('Sites', $Sites->ToArray());
			
			$Categories = $this->NewView('Base/Structure/List');
			$Categories->Template('T4M/Structure/EditArticlesCategoryList');
			$Categories->AddContent('Site', $Site->Get('ID'));
			$Categories->AddContent('Categories', 'all');
			$Categories->AddContent('StatusCategories', '0,1,2,3');
			$Categories->AddContent('LevelCategories', '0');
			$Categories->AddContent('UnpermittedCategories', 1);
			$Categories->AddContent('StatusArticles', NULL);
			$Categories->AddContent('LevelArticles', 0);
			$Categories->AddContent('UnpermittedArticles', 1);
			$Categories->Process();
			
			$CategoryItems = $Categories->Get('Items');
			$SelectedCategory = $this->Instance()->Get('Category');
			
			foreach ( $CategoryItems as $Key => $Value )
			{
				$CategoryItems[$Key]['ActionOpen'] = $this->Single('Base/Structure/Article')->Get('Url').'?Category='.$Value['ID'].'&Browser='.$_GET['Browser'];
				$CategoryItems[$Key]['Selected'] = 0;
			}
			
			if ( !$SelectedCategory )
			{
				$First = reset($CategoryItems);
				$SelectedCategory = $First['ID'];
			}
			
			$CategoryItems['C'.$SelectedCategory]['Selected'] = 1;
			
			$Categories->Set('Items', $CategoryItems);
			
			$this->Set('Categories', $Categories->Render());
			$this->Set('Browser', $_GET['Browser']);
			$this->Set('ActionLoad', $this->Single('Base/Structure/Article')->Get('Url').'?Category='.$SelectedCategory.'&Browser='.$_GET['Browser']);
		}
	}
}

?>