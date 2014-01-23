<?php

class Base_Core_View_Localizations extends Base_Core_View
{
	public function Process()
	{
		if ( $this->Single('Base/Access/User')->Get('Status') > 1 || !$this->Single('Base/Structure/Site')->Get('Localizations') ) return false;
		
		$Localizations = array();
		
		foreach ( explode(',', $this->Single('Base/Structure/Site')->Get('Localizations')) as $Localization )
		{
			if ( !$Data = System::Load('Base/Core/Localization/'.$Localization.'.json', true) ) continue;
			
			$Data = json_decode($Data, true);
			
			if ( !is_array($Data) ) continue;
			
			$Localizations[$Localization] = array(
				'Key' => $Localization,
				'Name' => $Data['Name'] ? $Data['Name'] : '{Name}',
				'Action' => $this->Action().'Select/'.$Localization
			);
		}
		
		$this->Set('Localizations', $Localizations);
		$this->MultiSet($Localizations[$this->Single('Base/Structure/Site')->Get('Localization')]);
	}
}

?>