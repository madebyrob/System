<?php

class Base_Core_Controller_Image extends Base_Core_Controller
{
	private $CacheType = 'jpg';
	
	public function Cache( $Address, $Width, $Height, $Quality = 100, $Background = '', $Method = 'fill', $Zoom = 'no', $CacheType = '' )
	{
		$Extern = preg_match('~^[a-z0-9]+:~i', $Address);
		
		if ( !$CacheType ) $CacheType = $this->CacheType;
		
		$Filename = md5($Address.$Width.$Height.$Quality.$Background.$Method.$Zoom.($Extern || !file_exists($Address) ? '' : filemtime($Address))).'.'.$CacheType;
		
		$Url = System::Url('Cache').$Filename;
		$Path = System::Path('Cache').$Filename;
		
		if ( file_exists($Path) ) return $Url;
		
		if ( $Extern )
		{
			$TempPath = System::Path('Temp').md5($Address).strtolower(strrchr($Address, '.'));
			
			copy($Address, $TempPath);
			
			$Image = $this->NewModel()->Load($TempPath);
			
			unset($TempImagePath);
		}
		else $Image = $this->NewModel()->Load($Address);
		
		if ( $Image )
		{
			$Image->Resize($Width, $Height, $Background, $Method, $Zoom)->Output($CacheType, $Path, $Quality);
			
			return $Url;
		}
		else return false;
	}
}

?>