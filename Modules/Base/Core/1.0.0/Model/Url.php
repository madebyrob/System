<?php

class Base_Core_Model_Url extends Base_Core_Model
{
	public function __construct()
	{
		protected $_Url;
		
		parent::__construct();
		
		$this->_Url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/';
	}
	
	public function Url( $Directory = '', $Absolute = false )
	{
		$Url = $this->_Url;
		
		$Parts = explode('/', $Directory);
		
		if ( $Parts[0] )
		{
			if ( $Parts[0] == 'System' )
			{
				$Url .= 'System/';
				
				array_shift($Parts);
			}
			
			if ( $Parts )
			{
				if ( count($Parts) == 1 && $Directory = self::Get('Directories', $Parts[0]) ) $Url .= $Directory;
				else return false;
			}
		}
		
		return $Absolute ? self::HostUrl().$Url : $Url;
	}
}

?>