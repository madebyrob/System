<?php

class Base_Structure_View_Pager extends Base_Core_View
{
	private $_Page = 1;
	
	private $_Total = 0;
	
	private $_Limit = 10;
	
	private $_Offset = 10;
	
	public function Process()
	{
		$TotalPages = ceil($this->_Total/$this->_Limit);
		
		if ( !$TotalPages ) $TotalPages = 1;
		
		$Pages = array();
		
		if ( $TotalPages > 1 && $this->_Page > $TotalPages ) $this->_Page = $TotalPages;
		
		if ( $this->_Page != 1 )
		{
			$this->Set('PreviousPage', $this->_Page-1);
	
			if ( $this->_Page > ( $this->_Offset + 2 ) ) $this->Set('FirstPage', 1);
			else
			{
				$Pages[1] = array(
					'Number' => 1,
					'Current' => 0
				);
			}
		}
		
		for ( $i = $this->_Page - $this->_Offset; $i <= ( $this->_Page + $this->_Offset ); ++$i )
		{
			if ( $i >= 1 && $i <= $TotalPages )
			{
				$Pages[$i] = array(
					'Number' => $i,
					'Current' => $this->_Page == $i ? 1 : 0
				);
			}
		}
		
		if ( $TotalPages > 1 && $this->_Page != $TotalPages )
		{
			$this->Set('NextPage', $this->_Page+1);
					
			if ( $this->_Page < ( $TotalPages - ( $this->_Offset + 1 ) ) ) $this->Set('LastPage', $TotalPages);
			else
			{
				$Pages[$TotalPages] = array(
					'Number' => $TotalPages,
					'Current' => 0
				);
			}
		}
		
		$this->Set('Pages', $Pages);
		$this->Set('Page', $this->_Page);
		$this->Set('TotalPages', $TotalPages);
		$this->Set('Total', $this->_Total);
		$this->Set('Limit', $this->_Limit);
		
		return $this;
	}
	
	public function Page( $Page )
	{
		$this->_Page = $Page;
		
		return $this;
	}
	
	public function Total( $Total )
	{
		$this->_Total = $Total;
		
		return $this;
	}
	
	public function Limit( $Limit )
	{
		$this->_Limit = $Limit;
		
		return $this;
	}
	
	public function Offset( $Offset )
	{
		$this->_Offset = $Offset;
		
		return $this;
	}
}

?>