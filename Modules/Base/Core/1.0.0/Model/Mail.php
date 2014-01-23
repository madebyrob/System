<?php

class Base_Core_Model_Mail extends Base_Core_Model
{
	private $_Subject = '';
	
	private $_Sender = '';
	
	private $_ReturnPath = '';
	
	private $_Receivers = array();
	
	private $_ReceiversCc = array();
	
	private $_ReceiversBcc = array();
	
	private $_ReplyTo = array();
	
	private $_Text = '';
	
	private $_Html = '';
	
	private $_Attachments = array();
	
	public function AddReceiver( $Receiver, $Type = '' )
	{
		if ( strtolower($Type) == 'cc' ) array_push($this->_ReceiversCc, $Receiver);
		elseif ( strtolower($Type) == 'bcc' ) array_push($this->_ReceiversBcc, $Receiver);
		elseif ( $Type == '' ) array_push($this->_Receivers, $Receiver);
		
		return $this;
	}
	
	public function AddReplyTo( $ReplyTo )
	{
		array_push($this->_ReplyTo, $ReplyTo);
		
		return $this;
	}
	
	public function Sender( $Sender )
	{
		$this->_Sender = $Sender;
		
		return $this;
	}
	
	public function Subject( $Subject )
	{
		$this->_Subject = $Subject;
		
		return $this;
	}
	
	public function Text( $Text )
	{
		$this->_Text = $Text;
		
		return $this;
	}
	
	public function Html( $Html )
	{
		$this->_Html = $Html;
		
		return $this;
	}
	
	public function AddAttachment( $Path )
	{
		if ( is_file($Path) ) array_push($this->_Attachments, $Path);
		
		return $this;
	}
	
	public function Send()
	{
		$Boundary = md5(time());
		
		//HEADERS
		$Headers = 'From: '.$this->_Sender.chr(10);
		$Headers .= 'Return-Path: '.($this->_ReturnPath ? $this->_ReturnPath : $this->_Sender).chr(10);
		
		if ( $this->_ReplyTo ) $Headers .= 'Reply-To: '.implode(', ', $this->_ReplyTo).chr(10);
		if ( $this->_ReceiversCc ) $Headers .= 'Cc: '.implode(', ', $this->_ReceiversCc).chr(10);
		if ( $this->_ReceiversBcc ) $Headers .= 'Bcc: '.implode(', ', $this->_ReceiversBcc).chr(10);
		
		$Headers .= 'X-Mailer: PHP/'.phpversion().chr(10);
		$Headers .= 'MIME-Version: 1.0'.chr(10);
		$Headers .= 'Content-Type: multipart/related; boundary="'.$Boundary.'"';
		
		//CONTENT
		$Content = '--'.$Boundary.chr(10);
		$Content .= 'Content-Type: multipart/alternative; boundary="'.$Boundary.'-1"'.chr(10);
		$Content .= chr(10);
		
		if ( $this->_Text )
		{
			$Content .= '--'.$Boundary.'-1'.chr(10);
			$Content .= 'Content-Type: text/plain; charset="utf-8"'.chr(10);
			$Content .= 'Content-Transfer-Encoding: 8bit'.chr(10);
			$Content .= chr(10);
			$Content .= $this->_Text.chr(10);
			$Content .= chr(10);
		}
		
		if ( $this->_Html )
		{
			$Content .= '--'.$Boundary.'-1'.chr(10);
			$Content .= 'Content-Type: text/html; charset="utf-8"'.chr(10);
			$Content .= 'Content-Transfer-Encoding: 8bit'.chr(10);
			$Content .= chr(10);
			
			$Images = '';
			
			while ( preg_match('~(src|background)="([^"]+\.(jpg|gif|png))"~i', $this->_Html, $Results) )
			{
				$Path =  preg_replace('~^'.System::Url().'~i', '', $Results[2]);
				
				if ( file_exists($Path) )
				{
					$Images .= '--'.$Boundary.chr(10);
					$Images .= 'Content-Type: image/'.$Results[3].chr(10);
					$Images .= 'Content-Transfer-Encoding: base64'.chr(10);
					$Images .= 'Content-ID: <'.md5($Results[2]).'>'.chr(10);
					$Images .= 'Content-Disposition: inline; filename='.substr($Results[2], strrpos($Results[2], '/')+1).chr(10);
					$Images .= chr(10);
					$Images .= chunk_split(base64_encode(file_get_contents($Path))).chr(10);
					$Images .= chr(10);
					
					$this->_Html = str_replace($Results[0], $Results[1].'="cid:'.md5($Results[2]).'"', $this->_Html);
				}
				else $this->_Html = str_replace($Results[0], $Results[1].'=""', $this->_Html);
			}
			
			$Content .= str_replace('href="'.System::Url(), 'href="'.System::Get('HostUrl').System::Url(), $this->_Html).chr(10);
			$Content .= chr(10);
			$Content .= '--'.$Boundary.'-1--'.chr(10);
			$Content .= chr(10);
			$Content .= $Images;
		}
		else
		{
			$Content .= '--'.$Boundary.'-1--'.chr(10);
			$Content .= chr(10);
		}
		
		foreach ( $this->_Attachments as $Attachment )
		{
			$File = fopen($Attachment, 'rb');
			
			$Content .= '--'.$Boundary.chr(10);
			$Content .= 'Content-Type: '.System::MimeType($Attachment).chr(10);
			$Content .= 'Content-Transfer-Encoding: base64'.chr(10);
			$Content .= 'Content-Disposition: attachment; filename='.substr($Attachment, strrpos($Attachment, '/')+1).chr(10);
			$Content .= chr(10);
			$Content .= chunk_split(base64_encode(fread($File, filesize($Attachment)))).chr(10);
			$Content .= chr(10);
			
			fclose($File);
		}
		
		$Content .= '--'.$Boundary.'--';
		
		return @mail(implode(', ', $this->_Receivers), '=?UTF-8?B?'.base64_encode($this->_Subject).'?=', $Content, $Headers);
	}
}

?>