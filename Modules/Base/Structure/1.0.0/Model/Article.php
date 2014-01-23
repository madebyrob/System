<?php

class Base_Structure_Model_Article extends Base_Core_Model
{
	protected $_Table = 'Articles';
	protected $_UseAttributes = true;
	
	public function LoadSingle()
	{
		if ( !$this->Single('Base/Structure/Category') ) return false;
		
		$Request = $this->Single('Base/Core/Request');
		$Filter = array('Status' => array('in' => array(4)));
		
		if ( count($Request->Get('UnresolvedParts')) < 2 )
		{
			$Key = reset($Request->Get('UnresolvedParts'));
			
			if ( $Key === false || $Key === '' || $Key === 'index.html' ) $Filter['Status']['in'][] = 3;
			else
			{
				$Parts = explode('.', $Key);
				
				$Filter[] = 'OR';
				$Filter[] = array('Key' => $Parts[0], 'ContentType' => ($Parts[1] ? $Parts[1] : ''), 'Status' => array('gt' => 0));
			}
		}
		
		$Articles = $this->NewCollection();
		$Articles->Set('Filter', array('Category' => $this->Single('Base/Structure/Category')->Get('ID'), $Filter));
		$Articles->Set('OrderBy', array('Status' => 'asc')); 
		$Articles->Set('Attributes', true);
		$Articles->Load();
		
		if ( $Article = $Articles->First() )
		{
			$Article->Set('Query', $_SERVER['QUERY_STRING']);
			
			if ( $Article->Get('Status') == 3 ) $Request->Assign('index.'.$this->Get('ContentType'));
			else $Request->Assign($Article->Get('Key').'.'.$Article->Get('ContentType'));
			
			if ( $Article->Get('Status') == 4 ) $Article->Set('Url', $this->Single('Base/Core/Request')->Get('Url'));
			
			return $Article;
		}
		
		return false;
	}
	
	public function Loaded()
	{
		$this->Set('Url', $this->Category()->Get('Url').($this->Get('Status') == 3 ? 'index' : $this->Get('Key')).'.'.$this->Get('ContentType'));
		
		$Permission = $this->Get('Permission');
		$Layout = $this->Get('Layout');
		
		if ( $Permission == '' ) $Permission = $this->Category()->Get('AutoPermission');
		if ( $Layout == '' ) $Layout = $this->Category()->Get('AutoLayout');
		
		$this->Set('AutoPermission', $Permission);
		$this->Set('AutoLayout', $Layout);
	}
	
	public function Category()
	{
		if ( $Category = $this->NewModel('Base/Structure/Category')->Tree()->Item($this->Get('Category')) ) return $Category;
		
		trigger_error('article "'.$this->Get('Name').' ('.$this->Get('ID').')" has no category', E_USER_ERROR);
		
		return false;
	}
	
	public function Instances( $Options = array() )
	{
		return $this->NewCollection('Base/Structure/Instance')->MultiSet(array_merge(array('Filter' => array('Article' => $this->Get('ID'))), $Options))->Load();
	}
	
	public function Send( $Options = array() )
	{
		$Mail = $this->NewModel('Base/Core/Mail');
		$Mail->Sender($Options['Sender']);
		
		if ( is_array($Options['Receiver']) )
		{
			foreach ( $Options['Receiver'] as $Receiver ) $Mail->AddReceiver($Receiver);
		}
		else $Mail->AddReceiver($Options['Receiver']);
		
		if ( isset($Options['Bcc']) ) foreach ( $Options['Bcc'] as $Bcc ) $Mail->AddReceiver($Bcc, 'bcc');
		
		$Query = $this->Single('Base/Access/User')->Get('Status') > 1 ? '?Internal&Action=/Base/Access/User/Login/'.base64_encode($this->Single('Base/Access/User')->Get($this->Single('Base/Structure/Site')->Get('LoginKey'))).'/'.base64_encode($this->Single('Base/Access/User')->Get('Password')) : '?Internal';
		
		if ( isset($Options['Query']) && is_array($Options['Query']) ) foreach ( $Options['Query'] as $Key => $Value ) $Query .= '&'.$Key.'='.urlencode($Value);
		
		if ( !$Content = file_get_contents(System::Get('HostUrl').$this->Get('Url').$Query) )
		{
			$this->Error('ArticleContentError');
			
			return false;
		}
			
		$Subject = $Options['Subject'] ? $Options['Subject'] : $this->Get('Name');
		
		$Options['Data']['Subject'] = $Subject;
		
		$Mail->Subject(preg_replace('~\{%(\w+)\}~ieU', '$Options["Data"]["\1"] ? $Options["Data"]["\1"] : ""', $Subject));
		
		$Html = preg_replace('~\{%(\w+)\}~ieU', '$Options["Data"]["\1"] ? $Options["Data"]["\1"] : ""', $Content);
		
		$Mail->Html($Html);
		$Mail->Text(strip_tags(preg_replace(array('~^.*<\!--\{ContentText\}-->(.*)<\!--\{/ContentText\}-->.*$~is', '~\s\s+~is', '~<br />|</tr>|</li>|</div>~i', '~</p>|</table>|</ul>|</h1>|</h2>|</h3>|</h4>|</h5>|</h6>~i', '~<a[^>]*href="(.+?)"[^>]*>(.+?)</a>~is', '~<li>~i', '~<td>~i'), array('\1', '', chr(10), chr(10).chr(10), '\2 (\1)', chr(9).'- ', chr(9).chr(9).chr(9)), $Html)));
		
		if ( $Options['Attachments'] )
		{
			foreach ( $Options['Attachments'] as $Attachment ) $Mail->AddAttachment($Attachment);
		}
		
		if( $Mail->Send() )
		{
			if ( $Options['ShowHints'] ) $this->Hint('ArticlesSent');
			
			return true;
		}
		
		if ( $Options['ShowErrors'] !== 0 ) $this->Error('ArticleSendError');
		
		return false;
	}
	
	public function Delete( $Hint = true )
	{
		$Instances = $this->NewCollection('Base/Structure/Instance')->Set('Filter', array('Article' => $this->Get('ID')))->Load();
		
		foreach ( $Instances->Items() as $Instance ) $Instance->Delete(false);
		
		return parent::Delete($Hint);
	}
}

?>