<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package			CodeIgniter
 * @author			Anthoplara [codinger]
 * @copyright		Copyright (c) 2013, codiLabs, Inc.
 * @since				Version 1.0
 * @email				appdev@codilabs.com
 * @filesource	//codilabs.github.com/acl-ci-library
 * @website			//codilabs.com
 */

// ------------------------------------------------------------------------

/**
 * Access Class
 *
 * @package			CodeIgniter
 * @subpackage	Libraries
 * @category		Access Control Lists
 * @author			codiLabs Dev Team
 */
class CI_Access {


	protected $ignored										= false;

	// to get controller name
	protected $uri_segment								= 1;

	// login path on your application
	protected $login_path									= 'user/login';

	// session name for initializing user is logged (true/false)
	protected $session_logged_name				= 'logged_in';

	// session name for get array access
	protected $session_access_name				= 'access';

	// name of menu table
	protected $table_menu									= 'menu';

	// fieldname of path menu on your menu table | ex. article or blog/read
	protected $segment_field							= 'menu_segment';

	/**
	* logged user allowed to access controller and user inherits from the role
	* ex. array('user'=> array('logout','notification'),'customer'=>array('view'));
	* allowed to access user/logout, user/notification and customer/view
	*/
	protected $ignored_access							= array();

	//name of query string to bring you referrer role before showing login page
	protected $redirect_query_string			= 'redirect';


	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 */
	public function __construct($params = array())
	{
		$CI =& get_instance();

		$this->session = $CI->session;
		$this->db = $CI->db;

		//get controller segment from uri
		$this->controller_segment = $CI->uri->segment($this->uri_segment);
		$this->function_segment = $CI->uri->segment($this->uri_segment+1);

		log_message('debug', "Access Class Initialized");
		$this->initialize();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize Preferences
	 *
	 * @access	public
	 * @param	array	initialization parameters
	 * @return	void
	 */
	function initialize()
	{

		//get request_uri
		$this->request_uri = str_replace($this->cleanBaseurl(),'',$this->curPageURL());

		//get controller and functioen name from  request uri
		$this->full_path = $this->controller_segment."/".$this->function_segment;

		//checking request page
		$this->request_page = ($this->function_segment)?$this->full_path:$this->controller_segment;



		// if user is not logged in and not in login path
		// user will be directing to login path with page path who was access
		if(!$this->is_logged() && $this->request_page != $this->login_path)
		{
			redirect(base_url().$this->login_path."?".$this->redirect_query_string."=".$this->request_uri);
			die();
		}

		//user will be directing to base_url if he was access login page
		if($this->is_logged() && $this->request_page == $this->login_path)
		{
			redirect(base_url());
			die();
		}

		//set access function
		$this->accessibility = $this->session->userdata($this->session_access_name);

		if(isset($this->ignored_access[$this->controller_segment]) && (in_array($this->function_segment, $this->ignored_access[$this->controller_segment])))
		{
			$this->ignored = true;
		}

		//get menu id of segment detected
		$this->menu_id = $this->get_menu_id();

		if($this->is_logged())
		{
			if(!$this->ignored)
			{
				if(!$this->is_read() || $this->menu_id==0) {
					show_404();
					die();
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Segment Checker
	 *
	 * @access	public
	 * @return	array
	 */
	function get_menu_id()
	{
		return $this->db->where($this->segment_field, $this->request_page)->count_all_results($this->table_menu);
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user is logged in
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_logged()
	{
		$result = FALSE;
		if($this->session->userdata($this->session_logged_name)) {
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in create access
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_create() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['create']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in readable
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_read() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['read']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in updatable
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_update() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['update']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in deletable
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_delete() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['delete']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in approvable
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_approve() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['approve']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Checking user in printable
	 *
	 * @access	public
	 * @return	boolean
	 */
	function is_print() {
		$result = FALSE;
		if(in_array($this->menu_id, $this->accessibility['print']))
		{
			$result = TRUE;
		}
		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Get modified base_url
	 *
	 * @access	public
	 * @return	char
	 */
	function cleanBaseurl() {
		$baseUrl = base_url();
		$parsSlash = explode('/', $baseUrl);
		$parsDQuote = explode(':', $parsSlash[2]);
		$cleanBaseUrl = '';
		if(count($parsDQuote)==1)
		{
			$cleanBaseUrl = $baseUrl;
		} else {
			for($i=0; $i<count($parsSlash); ++$i)
			{
				$slashesh = ($i==0)?'//':'/';
				if($parsSlash[$i]!='')
				{
					if($i==2) {
						$cleanBaseUrl .= $parsDQuote[0].$slashesh;
					} else {
						$cleanBaseUrl .= $parsSlash[$i].$slashesh;
					}
				}
			}
		}
		return $cleanBaseUrl;
	}

	// --------------------------------------------------------------------

	/**
	 * Get full url
	 *
	 * @access	public
	 * @return	char
	 */
	function curPageURL() {
		$pageURL = 'http';
		if(isset($_SERVER["HTTPS"]) AND $_SERVER["HTTPS"] == "on")
		{
			$pageURL .= "s";
		}
		$pageURL .= "://";
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		return $pageURL;
	}
}
// END Permission Class

/* End of file Access.php */
/* Location: ./application/libraries/Access.php */