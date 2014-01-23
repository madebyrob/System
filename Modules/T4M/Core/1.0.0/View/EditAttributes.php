<?php

class T4M_Core_View_EditAttributes extends Base_Core_View
{
	public function Process()
	{
		if ( is_numeric($_GET['Attribute']) )
		{
			$Attribute = $this->NewModel('Base/Core/Attribute')->Load($_GET['Attribute']);
			
			$this->MultiSet($Attribute->Get());
			$this->Set('BlockEdit', 1);
			$this->Set('ActionSave', $this->Action().'Save&Attribute='.$_GET['Attribute']);
			$this->Set('ActionDelete', $this->Action().'Delete/'.$_GET['Attribute']);
		}
		elseif ( $_GET['Model'] )
		{
			$this->Set('BlockMain', 1);
			$this->Set('Model', $_GET['Model']);
			$this->Set('ActionSave', $this->Action().'Save&Model='.$_GET['Model']);
			$this->Set('ActionDelete', $this->Action().'Delete&Model='.$_GET['Model']);
			
			$Attributes = $this->NewCollection('Base/Core/Attribute');
			$Attributes->Set('Filter', array('Model' => $_GET['Model']));
			$Attributes->Set('OrderBy', array('Key' => 'ASC'));
			$Attributes->Load();
			
			foreach ( $Attributes->Items() as $Attribute ) $Attribute->Set('ActionOpen', $this->Single('Base/Structure/Article')->Get('Url').'?Attribute='.$Attribute->Get('ID'));
			
			$this->Set('Attributes', $Attributes->ToArray());
		}
		else
		{
			$this->Set('BlockModels', 1);
			
			$Models = $this->NewCollection('Base/Core/Attribute');
			$Models->Set('Columns', array('Selector' => 'Model'));
			$Models->Set('GroupBy', array('Model'));
			$Models->Set('OrderBy', array('Model' => 'ASC'));
			$Models->Load();
			
			foreach ( $Models->Items() as $Item )
			{
				$Model = $this->NewModel($Item->Get('Selector'));
				
				$Item->Set('Name', $Model ? $Model->Translate('_Name'.$Model->Name()) : 'UNKNOWN MODEL');
				$Item->Set('ActionOpen', $this->Single('Base/Structure/Article')->Get('Url').'?Model='.$Item->Get('Selector'));
			}
			
			$this->Set('Models', $Models->ToArray());
		}
	}
}

?>