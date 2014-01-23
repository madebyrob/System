<?php

class Base_Core_View_Main extends Base_Core_View
{
	public function Process()
	{
		$Site = $this->Single('Base/Structure/Site');
		$Session = $this->Single('Base/Access/Session');
		$User = $this->Single('Base/Access/User');
		$Category = $this->Single('Base/Structure/Category');
		$Article = $this->Single('Base/Structure/Article');
		$Design = $this->Single('Base/Core/Design');
		$Layout = $this->Single('Base/Core/Layout');
		
		$this->MultiSet($Site->Get(), '_Site');
		$this->MultiSet($Session->Get(), '_Session');
		$this->MultiSet($User->Get(), '_User');
		$this->MultiSet($Category->Get(), '_Category');
		$this->MultiSet($Article->Get(), '_Article');
		$this->MultiSet($Design->Get(), '_Design');
		$this->MultiSet($Layout->Get(), '_Layout');
		
		$Containers = $Layout->Get('Containers');
		
		# layout container processing
		if ( $Containers && is_array($Containers) )
		{
			# get instances of current article
			$Instances = $Article->Instances(array('Attributes' => true));
			
			# walk through layout containers
			foreach ( $Containers as $Container )
			{
				# instance collection
				$Container['Collection'] = $this->NewCollection(false);
				
				# container output
				$Container['Output'] = array();
				
				# check container key
				if ( !isset($Container['Key']) || !preg_match('~^[a-z0-9]+$~i', $Container['Key']) ) continue;
				
				if ( $Instances )
				{
					# walk through article instances
					foreach ( $Instances->Items() as $Instance )
					{
						# check instance key
						if ( !preg_match('~^[a-z0-9]+$~i', $Instance->Get('Key')) || !$Instance->Get('Container') || $Instance->Get('Container') != $Container['Key'] ) continue;
						
						# add to instance collection
						$Container['Collection']->AddItem($Instance, $Instance->Get('Key'));
					}
				}
				
				if ( is_array($Container['Instances']) )
				{
					# walk through layout instances
					foreach ( $Container['Instances'] as $InstanceData )
					{
						# check instance key and if article instance exists
						if (  !isset($InstanceData['Key']) || !preg_match('~^[a-z0-9]+$~i', $InstanceData['Key']) || $Container['Collection']->Item($InstanceData['Key']) ) continue;
						
						# create new instance from layout data
						$Instance = $this->NewModel('Base/Structure/Instance');
						$Instance->MultiSet($InstanceData);
						$Instance->Set('Container', $Container['Key']);
						$Instance->Remove('Attributes');
						$Instance->Remove('Contents');
						
						if ( is_array($InstanceData['Attributes']) )
						{
							# walk through instance attributes
							foreach ( $InstanceData['Attributes'] as $AttributeData )
							{
								# check attribute key
								if (  !isset($AttributeData['Key']) || !preg_match('~^[a-z0-9]+$~i', $AttributeData['Key']) ) continue;
								
								# create new attribute from layout data
								$Attribute = $this->NewModel('Base/Core/Attribute');
								$Attribute->MultiSet($AttributeData);
								
								# add attribute to instance
								$Instance->AddAttribute($Attribute);
							}
						}
						
						if ( is_array($InstanceData['Contents']) )
						{
							$Contents = $this->NewCollection('Base/Structure/Content');
							
							# walk through instance contents
							foreach ( $InstanceData['Contents'] as $ContentData )
							{
								# check content key
								if (  !isset($ContentData['Key']) || !preg_match('~^[a-z0-9]+$~i', $ContentData['Key']) ) continue;
								
								# create new content from layout data
								$Content = $this->NewModel('Base/Structure/Content');
								$Content->MultiSet($ContentData);
								
								# add content to list
								$Contents->AddItem($Content);
							}
							
							# add list to instance
							$Instance->Contents($Contents);
						}
						
						# add to instance collection
						$Container['Collection']->AddItem($Instance, $InstanceData['Key']);
					}
				}
				
				# sort instances by sort key
				$Container['Collection']->Sort(array('Key' => 'SortKey'));
				
				# walk through combined instances
				foreach ( $Container['Collection']->Items() as $Instance )
				{
					if ( !$Instance->Get('Status') || ( $Instance->Get('Permission') != '' && !$this->NewModel('Base/Access/Group')->CheckMembership($Instance->Get('Permission')) ) ) continue;
					
					# load view of instance
					$View = $Instance->View();
					
					# check if view exists
					if ( !$View )
					{
						$this->Error('UnknownView', $Instance->Get('View'));
						
						continue;
					}
					
					# process view
					if ( $View->Process() === false ) continue;
					
					$View->MultiSet($Site->Get(), '_Site');
					$View->MultiSet($Session->Get(), '_Session');
					$View->MultiSet($User->Get(), '_User');
					$View->MultiSet($Category->Get(), '_Category');
					$View->MultiSet($Article->Get(), '_Article');
					$View->MultiSet($Design->Get(), '_Design');
					$View->MultiSet($Layout->Get(), '_Layout');
					
					# add so container output data
					$Container['Output'][] = array_merge($Instance->Get(), array('Content' => $View->Render()));
				}
				
				$this->Set($Container['Key'], $Container['Output']);
			}
		}
		
		# message processing
		$Messages = $Session->Get('Messages');
		
		$this->Set('Messages', $Messages);
		
		$Session->Remove('Messages');
		
		# set atricle content type
		$this->Type($Article->Get('ContentType'));
		
		# set layout template
		$this->Template($Layout->Get('Template'));
	}
}

?>