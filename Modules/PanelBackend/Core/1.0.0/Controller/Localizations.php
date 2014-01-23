<?php

class Base_Core_Controller_Localizations extends Base_Core_Controller
{
	function Select( $Localization )
	{
		if ( !preg_match('~^\w+$~', $Localization) ) return false;
		
		setcookie('SystemLocalization', $Localization);
		
		$_COOKIE['SystemLocalization'] = $Localization;
		
		$this->NewController('Base/Core/Main')->ProcessUrl();
	}
}

?>