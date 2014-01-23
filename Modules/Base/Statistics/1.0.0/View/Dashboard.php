<?php

class Base_Statistics_View_Dashboard extends Base_Core_View
{
	public function Process()
	{
		$Requests = $this->NewCollection('Base/Statistics/Main');
		$Requests->Set('OrderBy', array('CreateTime' => 'ASC'));
		$Requests->Load();
		
		$Visits = array();
		$Agents = array();
		
		foreach ( $Requests->Items() as $Request )
		{
			$Session = $Request->Get('Session');
			
			if ( !isset($Visits[$Session]) ) $Visits[$Session] = $Request->Get();
			
			$Visits[$Session]['Requests'][$Request->Get('CreateTime')] = $Request->Get();
			
			$Visits[$Session]['Requests'][$Request->Get('CreateTime')]['TimeOffset'] = $Request->Get('CreateTime')-$Visits[$Session]['CreateTime'];
			
			if ( preg_match('~Firefox~', $Request->Get('Agent')) ) $Agents['Firefox'] += 1;
			elseif ( preg_match('~Chrome~', $Request->Get('Agent')) ) $Agents['Chrome'] += 1;
			elseif ( preg_match('~MSIE~', $Request->Get('Agent')) ) $Agents['IE'] += 1;
			elseif ( preg_match('~Safari~', $Request->Get('Agent')) ) $Agents['Safari'] += 1;
			else $Agents['Other'] += 1;
		}
		
		$this->Set('Visits', $Visits);
		$this->Set('Requests', $Requests->Count());
		$this->Set('Agents', $Agents);
	}
}

?>