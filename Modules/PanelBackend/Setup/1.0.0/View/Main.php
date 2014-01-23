<?php

class Base_Setup_View_Main extends Base_Core_View
{
	public function Process()
	{
		System::Set('DisableModRewrite', 1);
		
		$Site = $this->NewModel('Base/Structure/Site');
		$Site->Set('Localization', 'de_DE');
		
		System::Set('Site', $Site);
		
		# select design
		$Design = $this->NewModel('Base/Core/Design')->Load('Backend', 1);
		
		System::Set('Design', $Design);
		
		$this->MultiSet($Design->Get(), '_Design');
		
		# set output type
		$this->Type('html');
		
		$Localizations = $this->NewView('Base/Core/Localizations');
		$Localizations->Process();
		
		$this->Set('Localizations', $Localizations->Render());
		
		# setup control
		$this->Set('SetupStart', 1);
		$this->Set('SetupCheck', 1);
		$this->Set('SetupSource', 1);
		$this->Set('SetupInfo', 1);
		$this->Set('SetupEnd', 1);
	}
}

?>