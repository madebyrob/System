<?php

class Base_Access_View_EditUsers extends Base_Core_View_Panel
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
			'Password' => array('Type' => 'Text'),
			'Name' => array('Type' => 'Text'),
			'EmailAddress' => array('Type' => 'Text'),
			'Groups' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('User', 3))
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
			'Password' => array('Type' => 'Password', 'Value' => ''),
			'Name' => array('Type' => 'Text'),
			'EmailAddress' => array('Type' => 'Text'),
			'Groups' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('User', 3))
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
			'EmailAddress' => array('Type' => 'Text'),
			'Groups' => array('Type' => 'Text'),
			'Status' => array('Type' => 'Select', 'Options' => $this->StatusArray('User', 3))
		);
		
		return $this->TranslateFields($Fields);
	}
}

?>