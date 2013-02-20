## CodeIgniter Access Control Lists Library
This is an CodeIgniter library to managing your user access.

### Merge with your application
* Add the file Access.php on libraries folder to your /application/libraries folder.
* If you haven't field for segment record in your table menus, alter it or add the sample table from file menu_table.sql 

You can now autoload the library or include it in one of your controllers at run time.
But, access library recommended to load automatically on your autoload config.

### What was needed?
At first, please set your login page on Access file 

	protected $login_path = 'user/login'; // here is your login page


Than, we need formated array access

	Array
	(
	    [create] => Array
	        (
	            [0] => 2
	            [1] => 3
	        )
	
	    [read] => Array
	        (
	            [0] => 1
	            [1] => 2
	            [2] => 3
	        )
	
	    [update] => Array
	        (
	            [0] => 3
	        )
	
	    [delete] => Array
	        (
	            [2] => 3
	        )
	
	    [approve] => Array
	        (
	            [0] => 1
	            [1] => 2
	        )
	
	    [print] => Array
	        (
	            [0] => 1
	            [1] => 2
	            [2] => 3
	        )
	
	)
if verify login proccess, set into session with userdata


###Functions List
* `is_logged` user logged status //autoused
* `is_create` create access detector
* `is_read` read access detector //autoused
* `is_update` update access detector
* `is_delete` delete access detector
* `is_approve` approve access detector
* `is_print` print access detector


###Example

	if($this->access->in_create())
	{
		//do active record functions that CodeIgniter provides to insert data to database
	}
