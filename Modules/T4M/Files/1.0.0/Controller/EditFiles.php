<?php

class T4M_Files_Controller_EditFiles extends Base_Core_Controller
{
	public $EarlyActions = array('CreateDirectory', 'DeleteDirectory', 'CreateFiles', 'DeleteFiles');
	
	public function CreateDirectory()
	{
		if ( !$_POST['CreateDirectory'] ) return false;
		
		$Directory = trim(base64_decode($_POST['CreateDirectory']['Path']), '/');
		$Path = System::Path('Files').($Directory ? $Directory.'/' : '');
		$Name = preg_replace('~[^\w_-]~i', '_', $_POST['CreateDirectory']['Name']);
		
		if ( strstr($Directory, '..') || !is_dir($Path) )
		{
			$this->Error('InvalidPath', $Path);
			
			return false;
		}
		
		if ( file_exists($Path.$Name) )
		{
			$this->Error('AlreadyExists', $Name);
			
			return false;
		}
		
		if ( !mkdir($Path.$Name) )
		{
			$this->Error('CreateDirectoryFailed', $Name);
			
			return false;
		}
		
		$this->Set('Directory', base64_encode(($Directory ? $Directory.'/' : '').$Name));
		$this->Hint('DirectoryCreated', $Name);
	}
	
	public function DeleteDirectory( $Directory )
	{
		if ( !$Directory = trim(base64_decode($Directory), '/') ) return false;
		
		if ( strstr($Directory, '..') || !is_dir(System::Path('Files').$Directory) )
		{
			$this->Error('InvalidPath', $Directory);
			
			return false;
		}
		
		if ( $this->NewView()->Directory($Directory) )
		{
			$this->Set('Directory', base64_encode($Directory));
			$this->Error('DirectoryNotEmpty', $Directory);
			
			return false;
		}
		
		rmdir(System::Path('Files').$Directory);
		
		$this->Hint('DirectoryDeleted', $Directory);
	}
	
	public function CreateFiles()
	{
		if ( !$_FILES['CreateFiles'] ) return false;
		
		$Directory = trim(base64_decode($_GET['Directory']), '/');
		$Path = System::Path('Files').($Directory ? $Directory.'/' : '');
		
		if ( strstr($Directory, '..') || !is_dir($Path) )
		{
			$this->Error('InvalidPath', $Directory);
			
			return false;
		}
		
		foreach ( $_FILES['CreateFiles']['name'] as $Key => $File )
		{
			if ( !$File ) continue;
			
			if ( !move_uploaded_file($_FILES['CreateFiles']['tmp_name'][$Key], $Path.preg_replace('~[^\w-_\.,+]~i', '_', $File)) )
			{
				$this->Warning('UploadFailed', $File);
			}
		}
		
		$this->Hint('FilesCreated');
		
		exit();
		
	}
	
	public function DeleteFiles()
	{
		if ( !$_POST['DeleteFiles'] ) return false;
		
		foreach ( $_POST['DeleteFiles'] as $Path )
		{
			if ( strstr($Path, '..') || !is_file(System::Path('Files').$Path) ) $this->Error('InvalidPath', $Path);
			
			unlink(System::Path('Files').$Path);
		}
		
		$this->Hint('FilesDeleted');
	}
}

?>