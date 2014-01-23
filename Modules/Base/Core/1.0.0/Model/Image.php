<?php

class Base_Core_Model_Image extends Base_Core_Object
{
	private $Image = NULL;
	
	public function Load( $Path )
	{
		$this->Image = $this->Open($Path);
		
		if ( $this->Image ) return $this;
		else return false;
	}
	
	public function Open( $Path )
	{
		if ( !file_exists($Path) ) return false;
		
		$Extension = strtolower(strrchr($Path, '.'));
		
		$Image = false;
		
		if ( $Extension == '.gif' ) $Image = imagecreatefromgif($Path);
		elseif ( $Extension == '.jpg' ) $Image = imagecreatefromjpeg($Path);
		elseif ( $Extension == '.png' ) $Image = imagecreatefrompng($Path);
		else trigger_error('Image type not supported: '.$Extension, E_USER_ERROR);
		
		return $Image;
	}
	
	public function Resize( $CanvasWidth = 0, $CanvasHeight = 0, $Background = '', $Method = 'stretch', $Zoom = 'no' )
	{
		$OriginalWidth = $NewWidth = imagesx($this->Image);
		$OriginalHeight = $NewHeight = imagesy($this->Image);
		
		$ResizingH = $CanvasWidth && $CanvasWidth < $OriginalWidth ? 1 : ( strtolower($Zoom) == 'yes' ? 1 : 0 );
		$ResizingV = $CanvasHeight && $CanvasHeight < $OriginalHeight ? 1 : ( strtolower($Zoom) == 'yes' ? 1 : 0 );
		
		if ( ( $CanvasWidth == $OriginalWidth && $CanvasHeight == $OriginalHeight ) || ( !$CanvasWidth && !$CanvasHeight ) )
		{
			$CanvasWidth = $OriginalWidth;
			$CanvasHeight = $OriginalHeight;
		}
		elseif ( strtolower($Method) == 'fit' )
		{
			if ( $ResizingH || $ResizingV )
			{
				$NewWidth = $CanvasWidth;
				$NewHeight = $CanvasHeight;
			}
			
			if ( $CanvasWidth && $CanvasHeight )
			{
				if ( ( $ResizingH || $ResizingV ) && ( $OriginalHeight/$CanvasHeight > $OriginalWidth/$CanvasWidth ) ) $NewWidth = ceil($OriginalWidth*($CanvasHeight/$OriginalHeight));
				elseif ( ( $ResizingH || $ResizingV ) && ( $OriginalHeight/$CanvasHeight < $OriginalWidth/$CanvasWidth ) ) $NewHeight = ceil($OriginalHeight*($CanvasWidth/$OriginalWidth));
			}
			elseif ( $CanvasWidth ) $CanvasHeight = $NewHeight = $ResizingH || $ResizingV ? ceil($OriginalHeight*($CanvasWidth/$OriginalWidth)) : $OriginalHeight;
			elseif ( $CanvasHeight ) $CanvasWidth = $NewWidth = $ResizingH || $ResizingV ? ceil($OriginalWidth*($CanvasHeight/$OriginalHeight)) : $OriginalWidth;
		}
		elseif ( strtolower($Method) == 'fill' )
		{
			if ( $ResizingH && $ResizingV )
			{
				$NewWidth = $CanvasWidth;
				$NewHeight = $CanvasHeight;
			}
			
			if ( $CanvasWidth && $CanvasHeight )
			{
				if ( $ResizingH && $ResizingV && $OriginalHeight/$CanvasHeight < $OriginalWidth/$CanvasWidth ) $NewWidth = ceil($OriginalWidth*($CanvasHeight/$OriginalHeight));
				elseif ( $ResizingH && $ResizingV && $OriginalHeight/$CanvasHeight > $OriginalWidth/$CanvasWidth ) $NewHeight = ceil($OriginalHeight*($CanvasWidth/$OriginalWidth));
			}
			elseif ( $CanvasWidth ) $CanvasHeight = $NewHeight = $ResizingH && $ResizingV ? ceil($OriginalHeight*($CanvasWidth/$OriginalWidth)) : $OriginalHeight;
			elseif ( $CanvasHeight ) $CanvasWidth = $NewWidth = $ResizingH && $ResizingV ? ceil($OriginalWidth*($CanvasHeight/$OriginalHeight)) : $OriginalWidth;
		}
		else
		{
			if ( $ResizingH ) $NewWidth = $CanvasWidth;
			if ( $ResizingV ) $NewHeight = $CanvasHeight;
			
			if ( $CanvasWidth && !$CanvasHeight ) $CanvasHeight = $OriginalHeight;
			if ( !$CanvasWidth && $CanvasHeight ) $CanvasWidth = $OriginalWidth;
		}
		
		if ( strtolower($Background) == 'image' )
		{
			$Canvas = imagecreatetruecolor($CanvasWidth, $CanvasHeight);
		
			$Background = imagecolorat($this->Image, 0, 0);
		}
		elseif ( strtolower($Background) == 'transparent' && substr(phpversion(), 0, 5) > '4.3.1' )
		{
			$Canvas = imagecreatetruecolor($CanvasWidth, $CanvasHeight);
		
			imagesavealpha($Canvas, true);
		
			$Background = imagecolorallocatealpha($Canvas, 0, 0, 0, 127);
		}
		elseif ( preg_match('~^[0-9a-f]{6}$~i', $Background) )
		{
			$Canvas = imagecreatetruecolor($CanvasWidth, $CanvasHeight);
		
			$Background = imagecolorallocate($Canvas, hexdec(substr($Background, 0, 2)), hexdec(substr($Background, 2, 2)), hexdec(substr($Background, 4, 2)));
		}
		else
		{
			$Canvas = imagecreatetruecolor($CanvasWidth, $CanvasHeight);
			
			$Background = imagecolorallocate($Canvas, 255, 255, 255);
		}
		
		imagefill($Canvas, 0, 0, $Background);
		
		imagecopyresampled($Canvas, $this->Image, floor(($CanvasWidth-$NewWidth)/2), floor(($CanvasHeight-$NewHeight)/2), 0, 0, $NewWidth, $NewHeight, $OriginalWidth, $OriginalHeight);
	
		$this->Image = $Canvas;
		
		return $this;
	}
	
	public function Watermark( $Path, $X = 0, $Y = 0 )
	{
		$Watermark = $this->Load($Path);
	
		$WatermarkWidth = imagesx($Watermark);
		$WatermarkHeight = imagesy($Watermark);
		
		if ( $X < 0 ) $X = imagesx($this->Image)-$WatermarkWidth+1+$X;
		elseif ( strtolower($X) === 'center' ) $X = (imagesx($this->Image)-$WatermarkWidth)/2;
		elseif ( !is_numeric($X) ) $X = 0;
		
		if ( $Y < 0 ) $Y = imagesx($this->Image)-$WatermarkHeight+1+$Y;
		elseif ( strtolower($Y) === 'center' ) $Y = (imagesy($this->Image)-$WatermarkHeight)/2;
		elseif ( !is_numeric($Y) ) $Y = 0;
		
		imagecopy($this->Image, $Watermark, $X, $Y, 0, 0, $WatermarkWidth, $WatermarkHeight);
		
		return $this;
	}
	
	public function Corners( $Path ) {
	
		$Corners = $this->Load($Path);
	
		$CornerWidth = imagesx($Corners)/2;
		$CornerHeight = imagesy($Corners)/2;
		$ImageWidth = imagesx($this->Image);
		$ImageHeight = imagesy($this->Image);
	
		imagecopy($this->Image, $Corners, 0, 0, 0, 0, $CornerWidth, $CornerHeight);
		imagecopy($this->Image, $Corners, ($ImageWidth-$CornerWidth), 0, $CornerWidth, 0, $CornerWidth, $CornerHeight);
		imagecopy($this->Image, $Corners, 0, ($ImageHeight-$CornerHeight), 0, $CornerHeight, $CornerWidth, $CornerHeight);
		imagecopy($this->Image, $Corners, ($ImageWidth-$CornerWidth), ($ImageHeight-$CornerHeight), $CornerWidth, $CornerHeight, $CornerWidth, $CornerHeight);
		
		return $this->Image;
	
	}
	
	public function Output( $Type = 'png', $Path = '', $Quality = 75 )
	{
		if ( $Type == 'gif' && function_exists('imagegif') )
		{
			if ( !$Path ) header('Content-type: image/gif');
			
			return imagegif($this->Image, $Path) ? true : false;
		}
		elseif ( $Type == 'jpg' && function_exists('imagejpeg') )
		{
			if ( !$Path ) header('Content-type: image/jpeg');
			
			return imagejpeg($this->Image, $Path, $Quality) ? true : false;
		}
		elseif ( $Type == 'png' && function_exists('imagepng') )
		{
			if ( !$Path ) header('Content-type: image/png');
			
			return imagepng($this->Image, $Path) ? true : false;
		}
		else exit('Image type output not supported');
		
		return false;
	}
}

?>