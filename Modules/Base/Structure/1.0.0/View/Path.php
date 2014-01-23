<?php

class Base_Structure_View_Path extends Base_Core_View
{
	public function Process()
	{
		$CurrentCategory = $this->Single('Base/Structure/Category');
		$Categories = array();
		
		foreach ( $CurrentCategory->Get('Path') as $Category ) $Categories[$Category] = $CurrentCategory->TreeItem($Category)->Get();
		
		$this->Set('Categories', $Categories);
	}
}

?>