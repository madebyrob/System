<?php

class Base_Structure_Controller_Article extends Base_Core_Controller
{
	public function Permalink( $ID )
	{
		if ( $Article = $this->NewModel()->Load($ID) ) $Url = $Article->Get('Url');
		else $Url = $this->Single('Base/Structure/Site')->Get('Url').$this->Single('Base/Structure/Site')->Get('UrlNotFound');
		
		echo $this->NewController('Base/Core/Main')->ProcessUrl(preg_replace('~^'.preg_quote(System::Url()).'~', '', $Url));
	}
}

?>