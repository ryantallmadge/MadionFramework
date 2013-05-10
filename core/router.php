<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * Routing Class
 */
 

class router{
	
	public $routes;//Will hold the routes from the launcher
	public $url;//Holds the URI from the server
	public $headertype;//Get the header type for the request being passed
	public $set_request;//Sets the request to the controller
	public $uri;//Sets the URI
	public $_REQUEST_URI;
	public static $userlogin = false;
	public static $companylogin = false;
	public static $push = false;

	public static $header_override = false;
	public static $header_type_sent = false;


	//Lets load the class and set the intail params
	function router($routes){	
		//Load the request URI
		$this->_REQUEST_URI = $_SERVER["REQUEST_URI"];
		//Load the routes
		$this->routes = $routes;
		//Load the Header Type
		$this->headertype = $this->getHeaderType();
		//Load the URL
		$this->url = array_filter($this->GetTheURI());		
		//return the method
		return true;
	}
	
	/**
	 *  Parse Routes
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @access	private
	 * @return	void
	 */
	function parse_routes()
	{
		// Turn the segment array into a URI string
		if(count($this->url) == 0){return $this->set_request = explode('/', $this->routes['default'][$this->headertype]);}
		
		$this->uri = implode('/', $this->url);

		// Is there a literal match?  If so we're done
		if (isset($this->routes[$this->uri][$this->headertype]))
		{
			return $this->set_request = explode('/', $this->checkLoginStat($this->routes[$this->uri][$this->headertype]));
		}

		// Loop through the route array looking for wild-cards
		foreach ($this->routes as $key => $val)
		{
			// Convert wild-cards to RegEx
			$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

			// Does the RegEx match?
			if (preg_match('#^'.$key.'$#', $this->uri))
			{
				// Do we have a back-reference?
				//if (strpos($val[$this->headertype], '$') !== FALSE AND strpos($key, '(') !== FALSE)
				//{
					//$val = preg_replace('#^'.$key.'$#', $val, $uri);
				//}
				if(isset($val[$this->headertype])){
					return $this->set_request = explode('/', $this->checkLoginStat($val[$this->headertype]));
				}
			}	
		}

		return false;

	}
	

	//Check if we need to be loged in to access this route
	//The other half of this is in the base controller
	function checkLoginStat($route){
		//Check if the routes needs to be logged in
		if(stristr($route,'|UL')){
			$route = str_replace('|UL','',$route);//Remove the |L from the route
			self::$userlogin = true;//Set the login flag to true
		}
		//Check if the routes needs to be logged in
		if(stristr($route,'|CL')){
			$route = str_replace('|CL','',$route);//Remove the |L from the route
			self::$companylogin = true;//Set the login flag to true
		}		
		//Check if we need to push the users when logged in
		if(stristr($route,'|P')){
			$route = str_replace('|P','',$route);//Remove the |P from the route
			self::$push = true;//Set the push flag to true
		}	
	  return $route;//Return the route
	}
	
	
	/*
	 * --------------------------------------------------------------------
	 * GET REQUEST TYPE - TRANSFORM OUR REQUEST IF TOLD TO DO SO
	 * --------------------------------------------------------------------
	 * This is how we find out what kind of request we are
	 * getting so we can route to the right controler
	 * the user can override this by adding .xml or .json to the URI
	 * if override we will take in the request as GET and output as the override
	 */
	
	function getHeaderType(){
		//Set our json transformer, if someone wants a page reutrned in json
		if(stristr($this->_REQUEST_URI, '.json')){	
			//remove the .json from the request so we can route the request later
			$this->_REQUEST_URI = str_replace('.json','',$this->_REQUEST_URI);
			self::$header_type_sent = 'GET';	//Set our header type to AJAX call
			self::$header_override = 'AJAX';//Set our override
		//Set our XML transformer, if someone wants a page reutrned in XML
		}elseif(stristr($_SERVER["REQUEST_URI"], '.xml')){
			//remove the .xml from the request so we can route the request later
			$this->_REQUEST_URI = str_replace('.xml','',$this->_REQUEST_URI);
			self::$header_type_sent = 'GET';	//Set our header type to XML call
			self::$header_override  = 'XML';//Set our override
		//If no one specified a header call, lets look at the server for the header	
		}else{
			if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
				self::$header_type_sent = 'AJAX';	//Set our header type to AJAX call
			}elseif(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST'){
				self::$header_type_sent = 'POST';	//Set our header type to POST call
			}elseif(isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET'){
				self::$header_type_sent = 'GET';	//Set our header type to GET call
			}else{
				//If we cant tell the header call method, die and send the an error...
				print_r($_SERVER['REQUEST_METHOD']);
				die("Can't get the request type");	
			}
		}
	 //return the header type
	 return self::$header_type_sent;
	}
	
	/*
	 * --------------------------------------------------------------------
	 * GRAB THE URI
	 * --------------------------------------------------------------------
	 * This is how we find out where we need to go and what we need to load
	 * Get the URI and pars the vars out of it
	 */
	
	 function GetTheURI(){
		$THE_URI = parse_url(urldecode($this->_REQUEST_URI));
		//This is for facebook login only, we need to parse a get command they send back, for some reason this is not a REST post
		//@TODO check and see if this can be done using REST
		if(isset($THE_URI['query']) AND stristr($THE_URI['query'], 'access_token')){
			parse_str($THE_URI['query'], $facebook_query);
			define("FACEBOOK_SESSION_QUERY", $facebook_query['session']);
		}else{
			define("FACEBOOK_SESSION_QUERY", '');
		}
		//Get the URI parts and set them to the global to be passed around
		$URI_PARTS = explode('/',str_replace('\\', '/',$THE_URI['path']));
		//If the URL has any kind of index.php/.html/.htm we will get an error, so we need to rebuild the URI without it
		if (   array_search('index.php',  $URI_PARTS) != 0 
			OR array_search('index.html', $URI_PARTS) != 0
			OR array_search('index.htm' , $URI_PARTS) != 0
			){
				for($i=0; $i < array_search($URI_PARTS[1], $URI_PARTS); $i++){$url_rebuild .=  $URI_PARTS[$i] . '/';}//rebuild URI	
				header("Location: " . $url_rebuild);//Send them to new URI
				exit;//Exit the script
		 }
	  //Return the URI parts
	  return $URI_PARTS;
	 }



}