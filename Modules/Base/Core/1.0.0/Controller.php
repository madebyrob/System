<?php

abstract class Base_Core_Controller extends Base_Core_Object
{
	protected $_Instance;
	
	public function Instance( $Instance = NULL )
	{
		if ( $Instance === NULL ) return $this->_Instance;
		
		$this->_Instance = $Instance;
		
		return $this;
	}
}

?>