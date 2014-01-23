<?php

class Base_Structure_Controller_EditSites extends Base_Core_Controller_Panel
{
	public $DefaultActionView = 'Base/Structure/EditCategories';
	
	public $Conditions = '`Status` < 4';
	
	public $Model = 'Base/Structure/Site';
	
	public function LoadOptions()
	{
		return array(
			
		);
	}
}

?>