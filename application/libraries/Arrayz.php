<?php
/**
* Array Manipulations - Inspired from Laravel Collection
* Developer - Giri Annamalai M
* Version - 1.0
*/
class Arrayz
{
	private $source;

	private $operator;

	public function __construct($array=[])
	{
		$this->source = [];
		if($this->_chk_arr($array))
		{
			$this->source = $array;
		}
	}

	/*
	* Object to callable conversion
	*/
	public function __invoke($source=[])
	{
		$this->source = $source;
		return $this;
	}

	/*
	* Match and return the array. supports regex
	*/
	public function pluck()
	{	
		$args = func_get_args();
		$search = $args[0];
		if($search !='')
		{			
			array_walk_recursive($this->source, function(&$value, &$key) use(&$search){				
				if( preg_match('/^'.$search.'/', $key) )
				{
					$this->intersected[][$key] = $value;
				}
			});	
			$this->source = $this->intersected;			
		}
		return $this;
	}

	/*
	* Like SQL Where . Supports operators. @param3 return actual key of element
	*/
	public function where()
	{
		$args = func_get_args();
		$op = [];
		$operator = '=';

		if (func_num_args() == 3) 
		{			    
			$search_key = $args[0];
			$operator = $args[1];
			$search_value = $args[2];
		}
		else			
		{			    
		    $search_key = $args[0];
		    $search_value = $args[1];
		}			

		$op = array_filter($this->source, function($src) use ($search_key, $search_value, $operator) {							 
			return $this->_operator_check($src[$search_key], $operator, $search_value);			  	
		},ARRAY_FILTER_USE_BOTH);				
					
		if(isset($args[3]) && $args[3])
		{
		  $this->source = $op;
		}
		else
		{
			$this->source = array_values($op);				
		}			
	
		return $this;
	}

	/*
	* Like SQL WhereIN . Supports operators.
	*/
	public function whereIn()
	{
		$args = func_get_args();		

		$op = [];
		if (func_num_args() == 2) 
		{			    
			$search_key = $args[0];
			$search_value = $args[1];
		}
		else			
		{
		    $search_key = $args[0];
		    $search_value = $args[1];			
		}

		foreach ($this->source as $k => $v) 
		{				
			if( @array_key_exists( $search_key, $v) && @in_array( $v[$search_key], $search_value ) )
			{	
				$op [] = $v;
			}
		}
		$this->source = $op;		
		return $this;
	}

	/*
	* search and return true. 
	*/
	public function contains()
	{
		$args = func_get_args();

		$isValid = false;

		if ( func_num_args() == 2 ) 
		{			    
			$search_key = $args[0];

			$search_value = $args[1];
		}
		else			
		{
			$search_key = '';

		    $search_value = $args[1];			
		}

		//If search value founds, to stop the iteration using try catch method for faster approach

		try {
			  array_walk_recursive($this->source, function(&$value, &$key) use(&$search_key, &$search_value){

		    	if($search_value != ''){

		    		if($search_value == $value && $key == $search_key){
		    			$isThere = true;	
		    		}
		    	}
		    	else
		    	{
		    		if($search_value == $value){
		    			$isThere = true;	
		    		}
		    	}
		    	// If Value Exists
		        if ($isThere) {
		            throw new Exception;
		        } 

		    });
		   }
		   catch(Exception $exception) {
			  $isValid = true;
		   }

		return $this->source = $isValid;
	}	


	/*
	* Converting Multidimensional Array into single array with/without null or empty 
	*/

	public function collapse()
	{
		$args = func_get_args();

		$empty_remove = !empty ($args[0]) ? $args[0] : false ;

		$op = [];
			
		array_walk_recursive($this->source, function(&$value, &$key) use(&$op, &$empty_remove){

			if( $empty_remove ){

				if( $value != '' || $value != NULL )
				{
					$op[][$key] = $value;					
				}
			}
			else
			{
				$op[][$key] = $value;
			}								
		});
		$this->source = $op;		
		return $this;		
	}

	/*
	* Converting Two Dimensional Array with lImit offset 
	*/

	public function limit()
	{
		$args = func_get_args();
		$limit = $args[0];
		$offset = !empty ($args[1]) ? $args[1] : 0 ;
		$op = [];
		$cnt = count($this->source);			
		if($limit > $cnt )	
		{
			$limit = $cnt;
		}
		$i = 0;
		if( $limit <= 1){
			$op[] = $this->source[$offset];
		}
		else
		{
			for($i=0; $i<$limit; $i++)
			{
				$op[] = $this->source[$offset];
				$offset++;
			}
		}
		$this->source = $op;
		return $this;
	}

	/*
	* Select keys and return only them
	* By selecting single array will return flat array
	* @param1: 'id, name, address', must be comma seperated.
	*/
	public function select()
	{
		$args = func_get_args();
		$select = $args[0];
		$op = [];		
		$select = explode(",", $select);		
		$i = 0;
		foreach ($this->source as $k => $v) 
		{
			foreach ($v as $key => $value) 
			{				
				if(in_array($key, $select))
				{
					if(count($select)>1)
					{
						$op[$i][$key] = $value;
					}else{
						$op[$i] = $value;							
					}
				}
			}		
			$i++;
		}
		$this->source = $op;
		return $this;
	}

	/*
	* Group by a key value 
	*/
	public function group_by()
	{
		$args = func_get_args();		
		$grp_by = $args[0];
		$op = [];
		foreach ($this->source as $data) {
		  $grp_val = $data[$grp_by];
		  if (isset($op[$grp_val])) {
		     $op[$grp_val][] = $data;
		  } else {
		     $op[$grp_val] = array($data);
		  }
		}
		$this->source = $op;
		return $this;
	}

	/*
	* Check with operators
	*/
    private function _operator_check($retrieved, $operator , $value)
	{
		switch ($operator) {
		    default:
		    case '=':
		    case '==':  return $retrieved == $value;
		    case '!=':
		    case '<>':  return $retrieved != $value;
		    case '<':   return $retrieved < $value;
		    case '>':   return $retrieved > $value;
		    case '<=':  return $retrieved <= $value;
		    case '>=':  return $retrieved >= $value;
		    case '===': return $retrieved === $value;
		    case '!==': return $retrieved !== $value;
		}
	}

	private function _chk_arr($array)
	{
		if(is_array($array) && count($array) > 0 )
		{
			return true;
		}
	}

	private function _recursive($array, $whr){

	    global $temp_data;

	    if(!empty($array)){

	    foreach($array as $key => $value){
	    //If $value is an array.
	        if(is_array($value)){
	            //We need to loop through it.
	            return $this->_recursive($value, $whr);
	        } else{                  
	               $temp_data[]= $key.'_'.$value;
	            }
	        }
	    }
	    return $temp_data;
	}

	/* Return output */
	public function get()
	{
		return $this->source;
	}

	/* Return array keys */
	public function keys()
	{		
		$this->source = array_keys($this->source);
		return $this;
	}
	
	/* Return array values */
	public function values()
	{		
		$this->source = array_values($this->source);
		return $this;
	}

	/*
	* Like SQL WhereIN . Supports operators.
	*/
	public function whereNotIn()
	{
		$args = func_get_args();		
		$op = [];
		if (func_num_args() == 2) 
		{			    
			$search_key = $args[0];
			$search_value = $args[1];
		}
		else			
		{
		    $search_key = $args[0];
		    $search_value = $args[1];			
		}
		foreach ($this->source as $k => $v) 
		{				
			if( @array_key_exists( $search_key, $v) && @!in_array( $v[$search_key], $search_value ) )
			{	
				$op [] = $v;
			}
		}
		$this->source = $op;			
		return $this;
	}

	/*
	* search the key exists and return true if found.
	*/
	public function has()
	{
		$args = func_get_args();

		$array = $args[0];

		$search_key = $args[1];

		$isValid = false;
		//If search value founds, to stop the iteration using try catch method for faster approach
		try {
			  array_walk_recursive($array, function(&$value, &$key) use(&$search_key){

	    		if($search_key == $key){
	    			$isThere = true;	
	    		}
		    	
		    	// If Value Exists
		        if ($isThere) {
		            throw new Exception;
		        } 

		    });
		   }
		   catch(Exception $exception) {
			  $isValid = true;
		   }
	    return $isValid;	 	
	}

	/* Select a key and sum its values. @param1: single key of array to sum */
	public function sum()
	{
		$args = func_get_args();		
		$op = [];
		$key = $args[0];
		$this->select($key);
		$this->source = array_sum($this->source);		
		return $this->source;
	}

	public function count()
	{
		return count($this->source);
	}
}
/* End of the file arrayz.php */
