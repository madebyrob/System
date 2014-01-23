<?php

class Base_Structure_View_Html extends Base_Core_View
{
	public function Contents()
	{
		$Contents = array(
			array('Key' => 'Content', 'Type' => 'Html', 'Value' => '')
		);
		
		return $Contents;
	}
	
	function Process()
	{
		$this->Set('Content', $this->Content('Content'));
	}
}

?>