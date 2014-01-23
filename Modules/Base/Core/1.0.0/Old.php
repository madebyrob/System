<?php

class Base_Core_Model_Old extends Base_Core_Model
{	
	public function Log( $Error )
	{	
		$ErrorFile = 'ErrorLog.txt';
	
		$File = fopen($ErrorFile, 'a+');
		
		@chmod($ErrorFile, 0777);
		
		fwrite($File, date('Y-m-d, H:i:s').' - '.str_replace("\n", '', $Error)."\n");
		
		fclose($File);
	}
	
	public function RedirectTo( $Address )
	{
		if ( !$_GET['SystemOutputData'] )
		{
			header('Location: '.$Address);
		
			exit;
		}
	}
	
	public function NumberFormat( $Number )
	{
		global $System;
		
		return number_format($Number, $System['Localization']['NumberDecimalDigits'], $System['Localization']['NumberDecimalSeparator'], $System['Localization']['NumberGroupSeparator']);
	}
		
	public function ArrayToXML( $Source )
	{	
		$XML = '';
		
		foreach ( $Source as $Key => $Value ) $XML .= ( is_array($Value) ? '<Array key="'.$Key.'">'.chr(10).ArrayToXML($Value).'</Array>' : '<Key'.$Key.'>'.str_replace('&', 'und', $Value).'</Key'.$Key.'>' ).chr(10);
		
		return $XML;
	}
	
	public function XMLToArray( $Source )
	{    
		if ( preg_match_all('~<(\w+)((?: \w+="(?:[^"]|\\\")*")*)(?: />|>(.*)</\1>)~siU', $Source, $Matches, PREG_SET_ORDER) )
		{        
			$Array = array();
			
			foreach ( $Matches as $Match )
			{            
				preg_match_all('~(\w+)="((?:[^"]|\\\")*)"~iU', $Match[2], $Attrubutes);
				
				$Array[$Match[1]][] = array(
					'Attributes' => $Attrubutes[1][0] && $Attrubutes[2][0] ? array_combine($Attrubutes[1], $Attrubutes[2]) : array(),
					'Value' => $Match[3] ? XMLToArray($Match[3]) : ''
				);
			}
			
			return $Array;
		}
		else return $Source;
	}
}

?>