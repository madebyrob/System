<?php

class T4M_Structure_Controller_EditCategories extends Base_Core_Controller
{
	public function Save()
	{
		if ( !$_POST['Category'] ) return false;
		
		$Category = $this->NewModel('Base/Structure/Category');
		
		if ( $_POST['Category']['ID'] )
		{
			$Category->Load($_POST['Category']['ID']);
			
			if ( $_POST['Category']['Attributes'] )
			{
				foreach ( $_POST['Category']['Attributes'] as $Key => $Value )
				{
					if ( $Category->Attribute($Key) ) $Category->Attribute($Key)->Set('Value', $Value);
				}
			}
		}
		
		$Category->MultiSet($_POST['Category']);
		
		if ( $Category->Save(false) ) $this->Hint($_POST['Category']['ID'] ? 'CategoryUpdated' : 'CategoryCreated', $Category->Get('Name'));
	}
	
	public function Delete( $ID )
	{
		if ( $ID && is_numeric($ID) )
		{
			$Category = $this->NewModel('Base/Structure/Category');
			
			if ( $Category->Load($ID) && $Category->Delete(false) ) $this->Hint('CategoryDeleted', $Category->Get('Name'));
		}
		elseif ( $_POST['DeleteCategory'] )
		{
			$Categories = $this->NewCollection('Base/Structure/Category');
			$Categories->Set('Filter', array('ID' => array('in' => $_POST['DeleteCategory'])));
			$Categories->Load();
			
			foreach ( $Categories->Items() as $Category )
			{
				if ( $Category->Delete(false) ) $this->Hint('CategoryDeleted', $Category->Get('Name'));
			}
		}
	}
}

?>