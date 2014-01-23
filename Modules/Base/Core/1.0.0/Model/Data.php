<?php

class Base_Core_Model_Data extends Base_Core_Object
{	
	private $_Connection;
	
	private $_Resource;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->Set('Host', System::Get('DatabaseHost'));
		$this->Set('Port', System::Get('DatabasePort'));
		$this->Set('User', System::Get('DatabaseUser'));
		$this->Set('Password', System::Get('DatabasePassword'));
		$this->Set('Database', System::Get('DatabaseName'));
		$this->Set('TablePrefix', System::Get('DatabaseTablePrefix'));
		$this->Set('Type', 'SELECT');
	}
		
	# ALIAS FUNCTIONS

	public function Type( $Type )
	{
		$this->Set('Type', $Type);
		
		return $this;
	}

	public function Columns( $Columns )
	{
		$this->Set('Columns', $Columns);
		
		return $this;
	}
	
	public function Tables( $Tables )
	{
		$this->Set('Tables', $Tables);
		
		return $this;
	}
	
	public function Filter( $Filter )
	{
		$this->Set('Filter', $Filter);
		
		return $this;
	}
	
	public function GroupBy( $GroupBy )
	{
		$this->Set('GroupBy', $GroupBy);
		
		return $this;
	}
	
	public function OrderBy( $OrderBy )
	{
		$this->Set('OrderBy', $OrderBy);
		
		return $this;
	}
	
	public function Limit( $Limit )
	{
		$this->Set('Limit', $Limit);
		
		return $this;
	}
	
	public function Data( $Data )
	{
		$this->Set('Data', $Data);
		
		return $this;
	}
	
	public function Host( $Host )
	{
		$this->Set('Host', $Host);
		
		return $this;
	}
	
	public function Port( $Port )
	{
		$this->Set('Port', $Port);
		
		return $this;
	}
	
	public function User( $User )
	{
		$this->Set('User', $User);
		
		return $this;
	}
	
	public function Password( $Password )
	{
		$this->Set('Password', $Password);
		
		return $this;
	}
	
	public function Database( $Database )
	{
		$this->Set('Database', $Database);
		
		return $this;
	}
	
	public function TablePrefix( $TablePrefix )
	{
		$this->Set('TablePrefix', $TablePrefix);
		
		return $this;
	}
	
	public function Debug( $Debug = false )
	{
		$this->Set('Debug', $Debug);
		
		return $this;
	}
	
	# SET EXTENDERS
	
	public function SetHost( $Host )
	{
		$this->Disconnect();
			
		return $Host;
	}
	
	public function SetPort( $Port )
	{
		$this->Disconnect();
			
		return $Port;
	}
	
	public function SetUser( $User )
	{
		$this->Disconnect();
			
		return $User;
	}
	
	public function SetPassword( $Password )
	{
		$this->Disconnect();
			
		return $Password;
	}
	
	public function SetDatabase( $Database )
	{
		$this->Disconnect();
			
		return $Database;
	}
	
	# CONNECTION
	
	private function Connection()
	{
		if ( !$this->_Connection ) $this->Connect();
		
		return $this->_Connection;
	}
	
	public function Connect()
	{
		if ( !$Host = $this->Get('Host') ) trigger_error('Missing database host');
		elseif ( !$Port = $this->Get('Port') ) trigger_error('Missing database port');
		elseif ( !$User = $this->Get('User') ) trigger_error('Missing database user');
		elseif ( !$Password = $this->Get('Password') ) trigger_error('Missing database password');
		elseif ( !$Name = $this->Get('Database') ) trigger_error('Missing database name');
		else
		{
			$this->_Connection = @new mysqli($Host, $User, $Password, $Name, $Port);
			
			if ( mysqli_connect_error() )
			{
				trigger_error('Connect Error ('.mysqli_connect_errno().'): '.mysqli_connect_error());
			}
			else
			{
				$this->_Connection->set_charset('utf8');
				
				return $this;
			}
		}
		
		return false;
	}
	
	public function Disconnect()
	{
		if ( $this->_Connection ) $this->_Connection->close();
	}
	
	# BUILD QUERY PARTS
	
	# build tables string
	public function TablesString()
	{
		$Tables = $this->Get('Tables');
		
		if ( is_array($Tables) && !empty($Tables) )
		{
			$Tables = '';
			
			foreach ( $this->Get('Tables') as $Key => $Value )
			{
				$Tables .= ', `'.$this->Get('TablePrefix').$Value.'`'.(!is_numeric($Key) ? ' AS `'.$Key.'`' : '');
			}
			
			$Tables = substr($Tables, 2);
		}
		elseif ( !is_string($Tables) ) return false;
		
		return $Tables;
	}
	
	# build filter string
	public function FilterString( $Data = NULL )
	{
		$Operators = array(
			'eq' => '=',
			'neq' => '!=',
			'lt' => '<',
			'lte' => '<=',
			'gt' => '>',
			'gte' => '>=',
			'in' => 'IN',
			'nin' => 'NOT IN',
			'like' => 'LIKE',
			'nlike' => 'NOT LIKE',
			'regexp' => 'REGEXP',
			'nregexp' => 'NOT REGEXP',
			'between' => 'BETWEEN'
		);
		
		if ( !$Data ) $Data = $this->Get('Filter');
		
		if ( is_array($Data) )
		{
			$Filter = '';
			
			$Operator = '';
			
			foreach ( $Data as $Key1 => $Value1 )
			{
				if ( is_string($Value1) && in_array(strtoupper($Value1), array('OR', 'AND', 'XOR')) )
				{
					$Operator = strtoupper($Value1);
					
					continue;
				}
				
				if ( is_array($Value1) )
				{
					if ( is_numeric($Key1) )
					{
						$Filter .= ($Operator ? ' '.$Operator.' ' : '').'('.$this->FilterString($Value1).')';
					}
					else
					{
						$Key1 = $this->Resolve($Key1);
						
						foreach ( $Value1 as $Key2 => $Value2 )
						{
							if ( is_string($Value2) && in_array(strtoupper($Value2), array('OR', 'AND', 'XOR')) )
							{
								$Operator = strtoupper($Value2);
								
								continue;
							}
							
							if ( !$Operators[$Key2] )
							{
								trigger_error('Filter invalid', E_USER_ERROR);
								
								return false;
							}
							
							if ( is_array($Value2) )
							{
								if ( $Key2 == 'in' || $Key2 == 'nin' )
								{
									foreach ( $Value2 as &$Value3 ) $Value3 = $this->Escape($Value3);
									
									$Filter .= ($Operator ? ' '.$Operator.' ' : '').$Key1.' '.$Operators[$Key2].' ("'.implode('", "', $Value2).'")';
								}
								else
								{
									foreach ( $Value2 as $Key3 => $Value3 )
									{
										if ( in_array(strtoupper($Value3), array('OR', 'AND', 'XOR')) )
										{
											$Operator = strtoupper($Value3);
											
											continue;
										}
										
										$Filter .= ($Operator ? ' '.$Operator.' ' : '').$Key1.' '.$Operators[$Key2].($Key2 == 'between' ? ' "'.$Key3.'" AND' : '').' "'.$this->Escape($Value3).'"';
										
										$Operator = 'AND';
									}
								}
							}
							elseif ( is_scalar($Value2) )
							{
								$Filter .= ($Operator ? ' '.$Operator.' ' : '').$Key1.' '.$Operators[$Key2].' '.(strstr($Value2, '__') ? $this->Resolve($Value2) : '"'.$this->Escape($Value2).'"');
							}
							else
							{
								trigger_error('Filter invalid', E_USER_ERROR);
								
								return false;
							}
							
							$Operator = 'AND';
						}
					}
				}
				elseif ( is_scalar($Value1) )
				{
					$Filter .= ($Operator ? ' '.$Operator.' ' : '').$this->Resolve($Key1).' = '.(strstr($Value1, '__') ? $this->Resolve($Value1) : '"'.$this->Escape($Value1).'"');
				}
				else return false;
				
				$Operator = 'AND';
			}
		}
		elseif ( is_string($Data) ) $Filter = $Data;
		elseif ( $Data !== NULL )
		{
			trigger_error('Filter invalid', E_USER_ERROR);
			
			return false;
		}
		
		return $Filter;
	}
	
	# BUILD QUERIES
	
	# select query by type
	public function Query()
	{
		if ( !$this->Get('Query') )
		{
			switch ( strtoupper($this->Get('Type')) )
			{
				case 'DELETE':
					$this->Set('Query', $this->QueryDelete());
					break;
				
				case 'UPDATE':
					$this->Set('Query', $this->QueryUpdate());
					break;
				
				case 'INSERT':
					$this->Set('Query', $this->QueryInsert());
					break;
				
				default:
					$this->Set('Query', $this->QuerySelect());
			}
		}
		
		return $this->Get('Query');
	}
	
	# build select string
	public function QuerySelect()
	{
		$Tables = $this->TablesString();
		
		if ( !$Tables ) return false;
		
		# build columns string
		$Columns = '';
		
		if ( is_array($this->Get('Columns')) )
		{
			foreach ( $this->Get('Columns') as $Key => $Value )
			{
				if ( is_array($Value) )
				{
					$Value[0] = strtoupper($Value[0]);
					
					if ( !in_array($Value[0], array('COUNT', 'SUM', 'MIN', 'MAX', 'LENGTH')) )
					{
						trigger_error('Columns invalid ('.$Key.')', E_USER_ERROR);
						
						return false;
					}
					
					$Columns .= ', '.$Value[0].'('.$this->Resolve($Value[1]).')';
				}
				else
				{
					$Columns .= ', '.$this->Resolve($Value);
				}
				
				if ( !is_numeric($Key) ) $Columns .= ' AS `'.$Key.'`';
			}
			
			$Columns = substr($Columns, 2);
		}
		elseif ( is_string($this->Get('Columns')) )
		{
			 $Columns = $this->Get('Columns');
		}
		elseif ( $this->Get('Columns') )
		{
			trigger_error('Columns invalid', E_USER_ERROR);
			
			return false;
		}
		
		# build filter string
		$Filter = $this->FilterString();
		
		# build group by string
		$GroupBy = '';
		
		if ( is_array($this->Get('GroupBy')) )
		{
			foreach ( $this->Get('GroupBy') as $Key => $Value )
			{
				$GroupBy .= ', '.$this->Resolve($Value);
			}
			
			$GroupBy = substr($GroupBy, 2);
		}
		elseif ( is_string($this->Get('GroupBy')) )
		{
			$GroupBy = '`'.$this->Get('GroupBy').'`';
		}
		elseif ( $this->Get('GroupBy') )
		{
			trigger_error('GroupBy invalid', E_USER_ERROR);
			
			return false;
		}
		
		# build order by string
		$OrderBy = '';
		
		if ( is_array($this->Get('OrderBy')) )
		{
			$OrderBy = '';
		
			foreach ( $this->Get('OrderBy') as $Key => $Dir )
			{
				$OrderBy .= ', ';
				$Key = $this->Resolve($Key);
				
				if ( is_array($Dir) )
				{
					$OrderBy .= 'CAST('.$Key.' AS '.strtoupper($Dir[1]).')';
					$Dir = $Dir[0];
				}
				else $OrderBy .= $Key;
				
				$OrderBy .= strtoupper($Dir) != 'DESC' ? ' ASC' : ' DESC';
			}
			
			$OrderBy = substr($OrderBy, 2);
		}
		elseif ( is_string($this->Get('OrderBy')) )
		{
			$OrderBy = '`'.$this->Get('OrderBy').'`';
		}
		elseif ( $this->Get('OrderBy') )
		{
			trigger_error('GroupBy invalid', E_USER_ERROR);
			
			return false;
		}
		
		# build limit string
		$Limit = '';
		
		if ( is_array($this->Get('Limit')) )
		{
			$Limit = implode(', ', $this->Get('Limit'));
		}
		elseif ( is_string($this->Get('Limit')) )
		{
			$Limit = $this->Get('Limit');
		}
		elseif ( $this->Get('Limit') )
		{
			trigger_error('Limit invalid', E_USER_ERROR);
			
			return false;
		}
		
		return 'SELECT '.($Columns ? $Columns : '*').' FROM '.$Tables.($Filter ? ' WHERE '.$Filter : '').($GroupBy ? ' GROUP BY '.$GroupBy : '').($OrderBy ? ' ORDER BY '.$OrderBy : '').($Limit ? ' LIMIT '.$Limit : '');
	}
	
	# build insert string
	public function QueryInsert()
	{
		if ( !$Table = reset($this->Get('Tables')) ) return false;
		
		# build colums string
		$Columns = '';
		
		if ( is_array($this->Get('Columns')) )
		{
			foreach ( $this->Get('Columns') as $Key => $Value ) $Columns .= ', `'.$Value.'`';
			
			$Columns = substr($Columns, 2);
		}
		elseif ( is_string($this->Get('Columns')) )
		{
			$Columns = $this->Get('Columns');
		}
		elseif ( $this->Get('Columns') )
		{
			trigger_error('Columns invalid', E_USER_ERROR);
			
			return false;
		}
		
		# build data string
		$Data = '';
		
		if ( is_array($this->Get('Data')) )
		{
			foreach ( $this->Get('Data') as $Key => $Value )
			{
				if ( !$this->Get('Columns') ) $Columns .= ', `'.$Key.'`';
				
				$Data .= ', "'.$this->Escape($Value).'"';	
			}
			
			$Data = substr($Data, 2);
			
			if ( !$this->Get('Columns') ) $Columns = substr($Columns, 2);
		}
		elseif ( is_string($this->Get('Data')) )
		{
			$Data = $this->Get('Data');
		}
		elseif ( $this->Get('Data') )
		{
			trigger_error('Data invalid', E_USER_ERROR);
			
			return false;
		}
		
		# build insert string
		return 'INSERT INTO `'.$this->Get('TablePrefix').$Table.'`'.($Columns ? ' ( '.$Columns.' )' : '').' VALUES ( '.$Data.' )';
	}
	
	# build update string
	public function QueryUpdate()
	{
		if ( !$Table = reset($this->Get('Tables')) ) return false;
		
		# build filter string
		$Filter = $this->FilterString();
		
		# build data string
		$Data = '';
		
		if ( is_array($this->Get('Data')) )
		{
			foreach ( $this->Get('Data') as $Key => $Value )
			{
				$Data .= ', `'.$Key.'` = "'.$this->Escape($Value).'"';
			}
			
			$Data = substr($Data, 2);
		}
		elseif ( is_string($this->Get('Data')) )
		{
			$Data = $this->Get('Data');
		}
		elseif ( $this->Get('Data') )
		{
			trigger_error('Data invalid', E_USER_ERROR);
			
			return false;
		}
		
		return 'UPDATE `'.$this->Get('TablePrefix').$Table.'` SET '.$Data.($Filter ? ' WHERE '.$Filter : '');
	}
	
	# build delete string
	public function QueryDelete()
	{
		if ( !$Table = reset($this->Get('Tables')) ) return false;
		if ( !$Filter = $this->FilterString() ) return false;
		
		return 'DELETE FROM `'.$this->Get('TablePrefix').$Table.'` WHERE '.$Filter;
	}
	
	# LOAD QUERY
	
	public function Load()
	{
		if ( !$this->Connection() || !$Query = $this->Query() ) return false;
		
		$Debug = array('Status' => 'OK', 'Query' => $Query);
		
		$this->_Resource = $this->_Connection->query($Query);
		
		if ( $this->_Connection->error )
		{
			trigger_error($this->_Connection->error.', Query: '.$Query, E_USER_ERROR);
			
			$Debug['Status'] = 'ERROR';
			$Debug['Error'] = $this->_Connection->error;
			
			return false;
		}
		elseif ( $this->Get('Type') == 'SELECT' ) $Debug['Results'] = $this->Count();
		
		System::Set('DatabaseRequests', System::Get('DatabaseRequests')+1);
		
		if ( $this->Get('Debug') ) System::Debug($Debug, 'SQL-DEBUG');
		
		return $this;
	}
	
	# OTHER
	
	# get last insert id
	public function InsertID()
	{
		return $this->Connection()->insert_id;
	}
	
	# get affected rows
	public function AffectedRows()
	{
		return $this->Connection()->affected_rows;
	}
	
	# escape strings for queries
	public function Escape( $String )
	{
		return $this->_Connection ? $this->Connection()->real_escape_string($String) : $String;
	}
	
	# escape strings for queries
	public function Resolve( $String )
	{
		$Tables = '';
		
		foreach ( $this->Get('Tables') as $Key => $Value ) $Tables .= '|'.(is_numeric($Key) ? $Value : $Key);
		
		if ( preg_match('~^('.substr($Tables, 1).')__(\w+|\*)$~', $String, $Matches) )
		{
			return '`'.$Matches[1].'`.'.($Matches[2] == '*' ? '*' : '`'.$Matches[2].'`');
		}
		elseif ( $String != '*' ) return '`'.$String.'`';
		
		return '*';
	}
	
	# fetch next row
	public function Next( $Numeric = false )
	{
		if ( !$this->_Resource ) return false;
		
		return $this->_Resource->fetch_array($Numeric ? MYSQLI_NUM : MYSQLI_ASSOC);
	}
	
	# count rows
	public function Count()
	{
		return $this->_Resource ? $this->_Resource->num_rows : false;
	}
}

?>