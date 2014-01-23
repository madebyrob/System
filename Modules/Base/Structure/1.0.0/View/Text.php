<?php

class Base_Structure_View_Text extends Base_Core_View
{
	public function Contents()
	{
		$Contents = array(
			array('Key' => 'Content', 'Type' => 'Text', 'Value' => '')
		);
		
		return $Contents;
	}
	
	function Process()
	{
		$this->Set('Content', $this->Content('Content'));
	}
}

?>