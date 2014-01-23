<?php

class T4M_Structure_Controller_EditArticles extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['Article'] ) return false;
		
		$Article = $this->NewModel('Base/Structure/Article');
		
		if ( $_POST['Article']['ID'] )
		{
			$Article->Load($_POST['Article']['ID']);
			
			if ( $_POST['Article']['Attributes'] )
			{
				foreach ( $_POST['Article']['Attributes'] as $Key => $Value )
				{
					if ( $Article->Attribute($Key) ) $Article->Attribute($Key)->Set('Value', $Value);
				}
			}
		}
		
		unset($_POST['Article']['Attributes']);
		
		$Article->MultiSet($_POST['Article']);
		
		if ( $Article->Save(false) ) $this->Hint($_POST['Article']['ID'] ? 'ArticleUpdated' : 'ArticleCreated', $Article->Get('Name'));
		
		if ( !$_POST['Instances'] ) return true;
		
		$Instances = $this->NewCollection('Base/Structure/Instance');
		$Instances->Set('Filter', array('ID' => array('in' => array_keys($_POST['Instances']))));
		$Instances->Set('Attributes', true);
		$Instances->Load();
		
		foreach ( $_POST['Instances'] as $ID => $Data )
		{
			if ( $Instance = $Instances->Item($ID) )
			{
				if ( $Data['Attributes'] )
				{
					foreach ( $Data['Attributes'] as $Key => $Value )
					{
						if ( $Instance->Attribute($Key) ) $Instance->Attribute($Key)->Set('Value', $Value);
					}
				}
				
				if ( $Data['Contents'] )
				{
					$Contents = $Instance->Contents();
					
					foreach ( $Data['Contents'] as $Key => $Value )
					{
						if ( $Contents->Item($Key) ) $Contents->Item($Key)->Set('Value', $Value)->Save(false);
					}
				}
			}
			else $Instance = $this->NewModel('Base/Structure/Instance');
			
			unset($Data['Attributes'], $Data['Contents']);
			
			$Instance->Set('Article', $Article->Get('ID'));
			$Instance->MultiSet($Data);
			$Instance->Save(false);
		}
	}
	
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$Article = $this->NewModel('Base/Structure/Article');
			
			if ( $Article->Load($ID) && $Article->Delete(false) ) $this->Hint('ArticleDeleted', $Article->Get('Name'));
		}
		elseif ( $_POST['DeleteArticle'] )
		{
			$Articles = $this->NewCollection('Base/Structure/Article');
			$Articles->Set('Filter', array('ID' => array('in' => $_POST['DeleteArticle'])));
			$Articles->Load();
			
			foreach ( $Articles->Items() as $Article )
			{
				if ( $Article->Delete(false) ) $this->Hint('ArticleDeleted', $Article->Get('Name'));
			}
		}
	}
	
	public function RemoveInstance( $ID )
	{
		if ( !$Instance = $this->NewModel('Base/Structure/Instance')->Load($ID) ) return false;
		
		$Instance->Delete(false);
		
		$this->Hint('InstanceDeleted', $Instance->Get('Key'));
		
		return true;
	}
}

?>