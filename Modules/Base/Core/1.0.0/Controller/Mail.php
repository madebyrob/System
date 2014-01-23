<?php

class Base_Core_Controller_Mail extends Base_Core_Controller
{
	public function Send()
	{
		$Mail = $this->NewModel('Base/Core/Mail')
		
		$Mail->Subject('Subject')->AddReceiver('arsch@g-e-c-o.net')->AddReceiver('cc@g-e-c-o.net', 'cc')->AddReceiver('mail@g-e-c-o.net')->Sender('bla@g-e-c-o.net')->Text('blub')->Html('<b>Bla</b><img src="/Designs/Default/Images/LogoShootingstar.png" />')->Send();
		
		$Mail->Html('<i>KRASS!!</i>')->Send();
	}
}

?>