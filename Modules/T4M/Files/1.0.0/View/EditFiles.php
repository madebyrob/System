<?php

class T4M_Files_View_EditFiles extends Base_Core_View
{
	public function Process()
	{
		if ( isset($_GET['Directory']) )
		{
			$this->Set('BlockDirectory', 1);
			
			$Files = array();
			$Directory = base64_decode($_GET['Directory']);
			
			if ( !strstr($Directory, '..') )
			{
				$Files = $this->Directory($Directory, 0, 'File');
				
				foreach ( $Files as $Key => $Value )
				{
					$Extension = strtolower(preg_replace('~^.*\.~', '', $Value['Name']));
					
					if ( in_array($Extension, array('png', 'jpg', 'jpeg', 'gif')) ) $Files[$Key]['Thumbnail'] = $this->NewController('Base/Core/Image')->Cache($Value['Path'], 75, NULL, 80, 'transparent', 'fit', 'no', 'png');
					
					$Files[$Key]['Size'] = number_format($Value['Size']/1024, 2, ',', '.').' KB';
					$Files[$Key]['Extension'] = $Extension;
				}
			}
			
			$this->Set('Directory', preg_replace('~^.*/~', '', trim($Directory, '/')));
			$this->Set('Files', $Files);
			$this->Set('ActionDeleteDirectory', $this->Action().'DeleteDirectory/'.base64_encode($Directory).'&Browser='.$_GET['Browser']);
			$this->Set('ActionDeleteFiles', $this->Action().'DeleteFiles&Directory='.$_GET['Directory'].'&Browser='.$_GET['Browser']);
			$this->Set('ActionCreateFiles', $this->Action().'CreateFiles&Directory='.$_GET['Directory'].'&Browser='.$_GET['Browser']);
			$this->Set('ActionReload', $this->Single('Base/Structure/Article')->Get('Url').'?Directory='.$_GET['Directory'].'&Browser='.$_GET['Browser']);
			$this->Set('MaximalUploadSize', ini_get('post_max_size'));
			$this->Set('MaximalFileSize', ini_get('upload_max_filesize'));
			$this->Set('Browser', $_GET['Browser']);
		}
		else
		{
			$this->Set('BlockMain', 1);
			
			$LastLevel = 0;
			$SelectedDirectory = $this->Instance()->Get('Directory');
			$Directories = $this->Directory('', 999, 'Directory');
			
			array_unshift($Directories, array('Key' => '', 'Name' => $this->Translate('RootDirectory'), 'Path' => System::Path('Files'), 'RelativePath' => '', 'Size' => 0, 'Type' => 'Directory', 'Level' => -1));
			
			foreach ( $Directories as $Key => $Value )
			{
				$Directories[$Key]['Level'] = ++$Value['Level'];
				
				if ( $LastLevel > $Value['Level'] ) $Directories[$Key-1]['Last'] = 1;
				elseif ( $LastLevel < $Value['Level'] )
				{
					$Directories[$Key]['First'] = 1;
					$Directories[$Key-1]['Submenu'] = 1;
				}
				elseif ( !$Key ) $Directories[$Key]['First'] = 1;
				
				if ( $LastLevel-$Value['Level'] > 0 ) $Directories[$Key]['PreviousLevelOffset'] = $LastLevel-$Value['Level'];
				
				$LastLevel = $Value['Level'];
				
				$Directories[$Key]['ActionOpen'] = $this->Single('Base/Structure/Article')->Get('Url').'?Directory='.$Value['Key'].'&Browser='.$_GET['Browser'];
				
				if ( $SelectedDirectory == $Value['Key'] ) $Directories[$Key]['Selected'] = 1;
			}
			
			$Directories[$Key]['Last'] = 1;
			
			$this->Set('SelectedDirectory', $SelectedDirectory);
			$this->Set('Directories', $Directories);
			$this->Set('ActionLoad', $this->Single('Base/Structure/Article')->Get('Url').'?Directory='.$SelectedDirectory.'&Browser='.$_GET['Browser']);
		}
		
		$this->Set('ActionCreateDirectory', $this->Action().'CreateDirectory&Browser='.$_GET['Browser']);
	}
	
	public function Directory( $Path, $Depth = 0, $Types = NULL )
	{
		if ( strstr($Path, '..') ) return false;
		
		if ( $Types && !is_array($Types) ) $Types = array($Types);
		
		if ( $Path ) $Path = rtrim($Path, '/').'/';
		
		$Level = 0;
		$Items = array();
		$ItemsRaw = array();
		$FullPath = System::Path('Files').$Path;
		$Directrory = opendir($FullPath);
		
		while ( $Item = readdir($Directrory) )
		{
			if ( $Item == '.' || $Item == '..' ) continue;
			
			$ItemsRaw[] = $Item;
		}
		
		natsort($ItemsRaw);
		
		foreach ( $ItemsRaw as $Item )
		{
			if ( is_dir($FullPath.$Item) ) $Type = 'Directory';
			elseif ( is_file($FullPath.$Item) ) $Type = 'File';
			elseif ( is_link($FullPath.$Item) ) $Type = 'Link';
			else $Type = 'Unknown';
			
			if ( $Types && !in_array($Type, $Types) ) continue;
			
			$Items[] = array('Key' => base64_encode($Path.$Item), 'Name' => $Item, 'Path' => $FullPath.$Item, 'RelativePath' => $Path.$Item, 'Size' => filesize($FullPath.$Item), 'Type' => $Type, 'Level' => $Level);
			
			if ( $Type == 'Directory' && is_numeric($Depth) && $Depth > 0 )
			{
				$Subdirectory = $this->Directory($Path.$Item, $Depth-1, $Types);
				
				foreach ( $Subdirectory as $Subitem )
				{
					$Subitem['Level'] += 1;
					
					$Items[] = $Subitem;
				}
			}
		}
		
		return $Items;
	}
}

?>