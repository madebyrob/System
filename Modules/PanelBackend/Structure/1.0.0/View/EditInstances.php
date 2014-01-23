<?php

class Base_Structure_View_EditInstances extends Base_Core_View_Panel
{
	public function Actions()
	{
		return array(
			'Delete' => 1
		);
	}
	
	public function FieldTitle()
	{
		return 'Key';
	}
	
	public function FieldsCreate()
	{
		$Fields = array(
			'Key' => array('Type' => 'Text'),
			'View' => array('Type' => 'Text'),
			'Container' => array('Type' => 'Text'),
			'Template' => array('Type' => 'Text'),
			'Article' => array('Type' => 'Select', 'Options' => $this->ArticleArray()),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Instance', 2))
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
			'View' => array('Type' => 'Text'),
			'Container' => array('Type' => 'Text'),
			'Template' => array('Type' => 'Text'),
			'Article' => array('Type' => 'Select', 'Options' => $this->ArticleArray()),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Instance', 2))
		);
		
		return $this->TranslateFields($Fields);
	}
	
	public function FieldsFilter()
	{
		$Fields = array(
			'ID' => 'Text',
			'CreateTime' => array('Type' => 'Text'),
			'CreateUser' => array('Type' => 'Text'),
			'UpdateTime' => array('Type' => 'Text'),
			'UpdateUser' => array('Type' => 'Text'),
			'Key' => array('Type' => 'Text'),
			'View' => array('Type' => 'Text'),
			'Container' => array('Type' => 'Text'),
			'Template' => array('Type' => 'Text'),
			'Article' => array('Type' => 'Select', 'Options' => $this->ArticleArray()),
			'Permission' => array('Type' => 'Text'),
			'SortKey' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('Instance', 2))
		);
		
		return $this->TranslateFields($Fields);
	}
	
	public function __construct()
	{
		parent::__construct();
		
		$this->Template('Base/Core/Panel');
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
}

?>