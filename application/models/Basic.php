<?php  if ( ! defined('BASEPATH')) exit('No Direct Script Access Allowed');

class Basic extends CI_Model
{			
	
	//=====================================================function parameter specifications begin===================================================
		
		// $table 	 =  name of the table (string)						Ex- 'user'
		// $select   =  select item (array)								Ex- array('id','name','email')			
		// $join 	 =  join condition (array)							Ex- array('role'=>"user.id=role.id,left",'vendor'=>"'user.id=vendor.id,left");
		// $data     =  data to be inserted,updated (array)				Ex- array('id'=>1,'name'=>'Al-amin','email'=>'jwel.cse@gmail.com')
		// $order_by =  order by (string)								Ex- 'id asc,name dsc'  // also array parameter accepted as $data[]
		// $group_by =  group by (string)								Ex- 'name'
		// $limit    =  upper limit (int)								Ex- 25
		// $start    =  lower limit (int)								Ex- 1
		// $id    	 =  id of a table (int)								Ex- 17

				



				//=========== where clause forming in details with examples begin==============



				/*
				// you can use >,>=,<,<= replacing != , if you need
				// you can use %match,match%,match replacing %match% , if you need
				// all array variables are named as they work (active record)

				
				// forms the where clause (active record)
				$where_simple =		array
									 (
									  	'id'						=> 1,
										'username'					=> 'alamin',
										'reference_id != '			=> 0,
										'role_id LIKE '				=> '%909%',
										'role_id NOT LIKE '			=> '%997509%'
									 );


				// forms the where_in clause (active record)
				$where_in =			array
									 (
									  	'id'						=> array(123,1,3),
									  	'reference_id'				=> array(123,1,3)
									 );



				// forms the where_not_in clause (active record)
				$where_not_in =		array
									 (
									  	'id'						=> array(44,55),
									  	'reference_id'				=> array(77,88)
									 );



				// forms the or_where clause (active record)
				$or_where =			array
									 (
									  	'role_id'					=> 1,
									  	'reference_id'				=> 'alamin',
									  	'id != '					=> 8787,
										'password LIKE '			=> '%7076%',
										'password NOT LIKE '		=> '%765666%'
									 );

				

				// forms the or_where clause (active record) but in a custom way
				// active record can not handle conditions such as : WHERE 'field' = 'match1' OR 'field' = 'match2' 
				// because it is passed by an array and there occours duplicate array index (field)
				// so here match value is used as array index and field name is used as corresponding value
				// it is not needed frequently but if you need you can use as shown below
				$or_where_advance =	array
									 (
									  	'123'						=> 'password',
									  	'2424'						=> 'password',
									  	 9							=> 'role_id'
									 );



				// forms the or_where_in clause (active record)
				$or_where_in =		array
									(
										'user_type_id'				=> array(123,1,3),
										'user_individual_type_id'	=> array(123,1,3)
									);



				// forms the or_where_not_in clause (active record)
				$or_where_not_in =	array
									(
										'user_type_id'      	  	=> array(44,55),
										'user_individual_type_id' 	=> array(77,88)
									);
				


				// forms the final where clause array				
				$where =			array
									(
										'where'						=> $where_simple,
										'where_in'					=> $where_in,
										'where_not_in'				=> $where_not_in,
										'or_where'					=> $or_where,
										'or_where_advance'			=> $or_where_advance,
										'or_where_in'				=> $or_where_in,
										'or_where_not_in'			=> $or_where_not_in
									);
				*/


				//=========== where clause forming in details with examples end==============
				





	//==================================================function parameter specifications end=========================================================





	public function generate_where_clause($where) //generates the joining clauses as given array
	{
		
		$keys = array_keys($where);  // holds the clause types. Ex- array(0=>'where',1=>'where_in'......................) 

		for($i=0;$i<count($keys);$i++)
		{
			if($keys[$i]=='where')
				$this->db->where($where['where']);  // genereates the where clauses

			else if($keys[$i]=='where_in')
			{
				$keys_inner = array_keys($where['where_in']); // holds the field names. Ex- array(0=>'id',1=>'username'......................) 
				for($j=0;$j<count($keys_inner);$j++) 
				{
					$field=$keys_inner[$j]; // grabs the field names
					$value=$where['where_in'][$keys_inner[$j]];	 // grabs the array values of the grabed field to be find in
					$this->db->where_in($field,$value);	//genereates the where_in clause	s				
				} //end for
				
			} //end else if

			else if($keys[$i]=='where_not_in') // works similar as where_in specified above
			{
				$keys_inner = array_keys($where['where_not_in']);
				for($j=0;$j<count($keys_inner);$j++)
				{
					$field=$keys_inner[$j];
					$value=$where['where_not_in'][$keys_inner[$j]];	
					$this->db->where_not_in($field,$value);	// genereates the where_not_in clauses					
				} // end for
				
			} // end else if

			else if($keys[$i]=='or_where')
				$this->db->or_where($where['or_where']); // genereates the or_where clauses

			else if($keys[$i]=='or_where_advance') // works similar as where_in but the array indexes & values are in reverse format as given parameter 
			{
				$keys_inner = array_keys($where['or_where_advance']);				
				for($j=0;$j<count($keys_inner);$j++)
				{
					$field=$where['or_where_advance'][$keys_inner[$j]];	
					$value=$keys_inner[$j];					
					$this->db->or_where($field,$value);	// genereates the or_where clauses								
				} // end for
				
			} // end else if

			else if($keys[$i]=='or_where_in') // works similar as where_in specified above
			{
				$keys_inner = array_keys($where['or_where_in']);
				for($j=0;$j<count($keys_inner);$j++)
				{
					$field=$keys_inner[$j];
					$value=$where['or_where_in'][$keys_inner[$j]];	
					$this->db->or_where_in($field,$value);	// genereates the or_where_in clauses					
				} // end for
				
			} // end else if

			else if($keys[$i]=='or_where_not_in') // works similar as where_in specified above
			{
				$keys_inner = array_keys($where['or_where_not_in']);
				for($j=0;$j<count($keys_inner);$j++)
				{
					$field=$keys_inner[$j];
					$value=$where['or_where_not_in'][$keys_inner[$j]];	
					$this->db->or_where_not_in($field,$value);	// genereates the or_where_not_in clauses					
				} // end for
				
			} // end else if			
		} // end outer for	

	}


	public function generate_joining_clause($join) //generates the joining clauses as given array
	{
		$keys = array_keys($join);
		for($i=0;$i<count($join);$i++)
		{
			$join_table=$keys[$i]; //gets the array key (this is the joining table's name)
			$join_condition_type=explode(',',$join[$keys[$i]]); //explodes the array value (separated by a comma - 1st part:joing condition and second part:joining type)
			$join_condition=$join_condition_type[0]; 
			$join_type=$join_condition_type[1];

			$this->db->join($join_table,$join_condition,$join_type); //forms the join clauses
		}
	}


	public function get_data($table,$where='',$select='',$join='',$limit='',$start=NULL,$order_by='',$group_by='',$num_rows=0,$csv='') //selects data from a table as well as counts number of affected rows
	{
				
		
		// only get data except deleted values
		// $col_name=$table.".deleted";
		// if($this->db->field_exists('deleted',$table) && $show_deleted==0)
		// $where['where'][$col_name]="0";
		
		
		$this->db->select($select);
		$this->db->from($table);
		
		if($join!='')					$this->generate_joining_clause($join);		
		if($where!='') 					$this->generate_where_clause($where);

		if($this->db->field_exists('deleted',$table))
		{
			$deleted_str=$table.".deleted";
			$this->db->where($deleted_str,"0");
		}
		
		if($order_by!='') 				$this->db->order_by($order_by);
		if($group_by!='') 				$this->db->group_by($group_by);

		
		

		if(is_numeric($start) || is_numeric($limit))
			$this->db->limit($limit, $start);
					
		$query=$this->db->get();
		
		if($csv==1)
		return $query; //csv generation requires resourse ID
		
		$result_array=$query->result_array(); //fetches the rows from database and forms an array[]
		
		if($num_rows==1)
		{
			$num_rows=$query->num_rows(); //counts the affected number of rows
			$result_array['extra_index']=array('num_rows'=>$num_rows);	//addes the affected number of rows data in the array[]
		}
		
		// print_r($this->db->last_query());
		return $result_array; //returns both fetched result as well as affected number of rows 
		
	}

	public function count_row($table,$where='',$count='id',$join='',$group_by='') //counts data from a table
	{
		/*$count_str="COUNT(".$count.") as total_rows";		*/
		$this->db->select($count);
		$this->db->from($table);

		if($join!='')					$this->generate_joining_clause($join);		
		if($where!='') 					$this->generate_where_clause($where);

		if($this->db->field_exists('deleted',$table))
		{
			$deleted_str=$table.".deleted";
			$this->db->where($deleted_str,"0");
		}
		
		if($group_by!='') 				$this->db->group_by($group_by);
							
		$query=$this->db->get();	

		$num_rows = $query->num_rows();
				
		$result_array[0]['total_rows']=$num_rows; 
		
		return $result_array; 
	}






	function insert_data($table,$data)  //inserts data into a table 
	{
		$this->db->insert($table,$data);
		return true;			
	}



	
	function update_data($table,$where,$data) //updates data of a table 
	{
		if($where!='') $this->db->where($where);
		$this->db->update($table,$data);
		return true;
	}
	


	
	function delete_data($table,$where) //deletes data from a table 
	{
		$this->db->where($where);
		$this->db->delete($table);
		return true;
	}		

	function execute_query($sql) //executes custom sql query
	{		
		$query=$this->db->query($sql);
		return $query->result_array();
	}	


	

	function execute_complex_query($sql) //executes complex custom sql query
	{		
		return $query=$this->db->query($sql);
	}	




	function is_active($table,$where='') // checks a row's status of a table is active or not, returns true if active
	{
		$this->db->select('status');
		$this->db->from($table);
		$where['status']=1;
		$this->db->where($where);
		$query=$this->db->get();		
		$num_rows=$query->num_rows();
		
		if ($num_rows>0) return true;
		else return false;
	}



	function is_exist($table,$where='',$select='') //checks a row is exist or not, returns true if exists
	{		
		$this->db->select($select);
		$this->db->from($table);
		if($where!='') $this->db->where($where);
		$query=$this->db->get();
		$num_rows=$query->num_rows();		
		if($num_rows>0) return TRUE;
		else return FALSE;	
	}	
	


	function is_unique($table,$where='',$select='') //checks if a row is unique or not , returns true if unique
	{		
		$this->db->select($select);
		$this->db->from($table);
		if($where!='') $this->db->where($where);
		$query=$this->db->get();
		$num_rows=$query->num_rows();
		
		if($num_rows>0) return FALSE;
		else return TRUE;	
	}


	function get_enum_values($table_name="",$column_name="") //return array of enum values of a field in a table
	{
		$empty_array=array();
		
		if($table_name=="" || $column_name=="")
		return $empty_array();

		$sql="SHOW COLUMNS FROM $table_name WHERE Field = '$column_name'";
		$results=$this->execute_query($sql);

		$enumList = explode(",", str_replace("'", "", substr($results[0]['Type'], 5, (strlen($results[0]['Type'])-6))));
		return $enumList;	
	}

	function get_enum_values_assoc($table_name="",$column_name="") //return array of enum values of a field in a table
	{
		if($table_name=="" || $column_name=="")
		return array();

		$sql="SHOW COLUMNS FROM $table_name WHERE Field = '$column_name'";
		$results=$this->execute_query($sql);

		$enumList = explode(",", str_replace("'", "", substr($results[0]['Type'], 5, (strlen($results[0]['Type'])-6))));

		$enumList_final=array();
		foreach ($enumList as $key => $value) 
		{
			$enumList_final[$value] = $value;
		}
		return $enumList_final;	
	}
	
	
	/**
    * method to DUMP DATA
    * @access public
    * @return boolean
    * @param string	
    */
    public function import_dump($filename = '')
    {
        if ($filename=='') {
            return false;
        }
        if (!file_exists($filename)) {
            return false;
        }
        
        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file($filename);

        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                $this->execute_complex_query($templine);
                // Reset temp variable to empty
                $templine = '';
            }
        }
        return true;
    }
		
	
}
	