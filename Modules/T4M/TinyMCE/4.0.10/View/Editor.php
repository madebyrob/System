<?php

class T4M_TinyMCE_View_Editor extends Base_Core_View
{
	public function Process()
	{
		$Path = preg_replace('~^'.System::Path('System/Modules').'('.$this->ModuleGroup().'/'.$this->Module().'/\d+(\.\d+)+?/).*$~', '\1', dirname(__FILE__));
		$Url = System::Url('System/Modules').$Path;
		
		$this->Set('ScriptUrl', $Url.'JavaScript/');
	}
}

?>