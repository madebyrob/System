<?php

class Base_Structure_View_EditCategories extends Base_Core_View_Panel
{
	public function __construct()
	{
		parent::__construct();
		
		$this->Template('Base/Core/Panel');
	}
	
	public function Actions()
	{
		return array(
			'Delete' => 1
		);
	}
	
	public function FieldTitle()
	{
		return 'Name';
	}
	
	public function FieldsCreate()
	{
		$Fields = array(
			'Key' => array('Type' => 'Text'),
			'Name' => array('Type' => 'Text'),
			'Site' => array('Type' => 'Select', 'Options' => $this->SiteArray()),
			'Parent' => array('Type' => 'Select', 'Options' => $this->CategoryArray()),
			'Inherit' => array('Type' => 'Select', 'Options' => array_merge(array(array('Value' => '', 'Name' => '---')), $this->ArticleArray())),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Category', 3))
		);
		
		return $this->TranslateFields($Fields);
	}
	
	public function FieldsUpdate()
	{
		$Fields = array(
			'ID' => array(),
			'CreateTime' => array(),
			'CreateUser' => array(),
			'UpdateTime' => array(),
			'UpdateUser' => array(),
			'Key' => array('Type' => 'Text'),
			'Name' => array('Type' => 'Text'),
			'Site' => array('Type' => 'Select', 'Options' => $this->SiteArray()),
			'Parent' => array('Type' => 'Select', 'Options' => $this->CategoryArray()),
			'Inherit' => array('Type' => 'Select', 'Options' => array_merge(array(array('Value' => '', 'Name' => '---')), $this->ArticleArray())),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Category', 3))
		);
		
		return $this->TranslateFields($Fields);
	}
	
	public function FieldsFilter()
	{
		$Fields = array(
			'ID' => array('Type' => 'Text'),
			'CreateTime' => array('Type' => 'Text'),
			'CreateUser' => array('Type' => 'Text'),
			'UpdateTime' => array('Type' => 'Text'),
			'UpdateUser' => array('Type' => 'Text'),
			'Key' => array('Type' => 'Text'),
			'Name' => array('Type' => 'Text'),
			'Site' => array('Type' => 'Select', 'Options' => $this->SiteArray()),
			'Parent' => array('Type' => 'Select', 'Options' => $this->CategoryArray()),
			'Inherit' => array('Type' => 'Select', 'Options' => array_merge(array(array('Value' => '', 'Name' => '---')), $this->ArticleArray())),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Category', 3))
		);
		
		return $this->TranslateFields($Fields);
	}
	
	public function ArticleArray()
	{
		$Articles = array();
		
		foreach ( $this->NewCollection('Base/Structure/Article')->Load()->Items() as $Article )
		{
			$Articles[] = array(
				'Value' => $Article->Get('ID'),
				'Name' => $Article->Get('Url').' • '.$Article->Get('Name')
			);
		}
		
		return $Articles;
	}
	
	public function CategoryArray()
	{
		$Categories = array();
		$Str = '- ';
		
		foreach ( $this->NewModel('Base/Structure/Category')->Tree(NULL, 0, 2)->Items() as $Category )
		{
			$Categories[] = array(
				'Value' => $Category->Get('ID'),
				'Name' => $Category->Get('Url').' • '.$Category->Get('Name')
			);
		}
		
		return $Categories;
	}
	
	public function SiteArray()
	{
		$Sites = array();
		
		foreach ( $this->NewCollection('Base/Structure/Site')->Load()->Items() as $Site )
		{
			$Sites[] = array(
				'Value' => $Site->Get('ID'),
				'Name' => $Site->Get('Url').' • '.$Site->Get('Name')
			);
		}
		
		return $Sites;
	}
}

?>