<?php

class Base_Structure_View_NotFound extends Base_Core_View
{
	public function Process()
	{
		preg_match('~/([a-z0-9-_]+)([^/]*?|/)$~i', System::RequestUrl(), $Matches);
		
		if ( $Matches[1] )
		{
			$Suggestions = $this->NewCollection('Base/Structure/Article');
			$Suggestions->Set('Filter', array('Key' => array('like' => '%'.$Matches[1].'%'), 'Status' => array('in' => array(2, 3))));
			$Suggestions->Load();
			
			foreach ( $this->NewModel('Base/Structure/Category')->SiteTree() as $Category )
			{
				if ( preg_match('~'.$Matches[1].'~', $Category->Get('Key')) ) $Suggestions->AddItem($Category->Duplicate());
			}
			
			foreach ( $Suggestions->Items() as $Key => $Suggestion )
			{
				if ( !$this->NewModel('Base/Access/Group')->CheckMembership($Suggestion->Get('AutoPermission')) ) $Suggestions->RemoveItem($Key);
			}
			
			$this->Set('Suggestions', $Suggestions->ToArray());
		}
	}
}

?>