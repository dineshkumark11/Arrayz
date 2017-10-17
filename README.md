# Arrayz
Array manipulation library for Codeigniter 3.x
Arrayz Library Functions:
------------------------
Created for two dimensional associative array / result array from codeigniter.

1. Load library and create instance: 

$this->load->library('Arrayz');
$arrayz = new Arrayz;

2. After instance created,

$arrayz($array)->where('id','1')->get();

3. get() is required output the reponse from the library, only if the reponse is array.

Example Array:
--------------

$array = array (
  0 => 
  array (
   'id' =>'11',   
   'Name' =>'Giri',
   'SSN' =>'123524',   
   'street' =>'17 west stree',
   'state' =>'NY',
   'created_date' =>'0000-00-00 00:00:00',
  ),
  1 => 
  array (
   'id' =>'11',   
   'Name' =>'Anna',
   'SSN' =>'56789',   
   'street' =>'18 west stree',
   'state' =>'CA',
   'created_date' =>'0000-00-00 00:00:00',
  ),
);

select:
-------
	
      $arrayz($array)->select('id')->get(); //Select the key found returns id as flat array

      $arrayz($array)->select('id,name')->get(); //Select the key found returns  id, name


Pluck:
------    
      $arrayz($array)->pluck('st')->get(); //Support RegEx key which are matching 'st' and returns street, state          
       
      Most usable case is When Posting ($_POST) Iterator based elements we can use this. count_1, count_2

Where:
------
      $arrayz($array)->where('id' ,'1')->get(); // Will return the array where matches id is 1 

      $arrayz($array)->where('id' ,'>','3')->get(); //Will return the array where id is greater than 3, =,!=, >, <>, >=, <=, === operators are supported. By default '='.

WhereIn: 
------
      $arrayz($array)->where( 'id', ['1','3'] )->get(); // Will return the array where matches id is 34 and 35

WhereNotIn: 
------
      $arrayz($array)->where('id', ['34','35'] )->get(); // Will return the array where not matches id is 34 and 35

contains:
--------- 
      $arrayz($array)->contains('id','34')->get(); //Search for the value id in 34. if found return true else false.

      $arrayz($array)->contains('34')->get(); //Search for the value 34. if found return true else false.

collapse:
---------
      $arrayz($array)->collapse($array)->get(); //flatten multidimensional array into single array

limit:
------
      $arrayz($array)->limit(10)->get(); //Will return the first 10 elements
      $arrayz($array)->limit( 10, 5)->get(); //Will return the 10 elements after the 5 the index (Offset)

group_by: 
---------
      Groupby by mentioned Key, similar to sql;
      
      $arrayz($array)->group_by('id')->get(); // Will return the array group by by fmo id

has:
----
      $arrayz($array)->has('id')->get(); //When the key found returns true


Sum:
----
      $arrayz($array)->sum('id'); //Sum the value of id in given array

Keys:
----
      $arrayz($array)->keys()->get(); //Returns the key of the array. similar to array_keys

Values:
-------
      $arrayz($array)->values()->get(); //Returns the values of the array. similar to array_values

Count:
------
     $arrayz($array)->count(); //Returns the no of array/elements based on the array. similar to array count()


This is initiation to show, we can integrate or acheive other frameworks features in Codeigniter.
