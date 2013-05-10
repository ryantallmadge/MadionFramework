<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * This is where we break a part the request and launch the controllers.
 *
 */

/*
 * --------------------------------------------------------------------
 * LOAD THE ROUTER MANAGMENT FILE
 * --------------------------------------------------------------------
 * 
 */
	//Load the routes controller
	require_once COREDIR.'/router'.EXT;

/*
 * --------------------------------------------------------------------
 * LOAD OUR ROUTES TABLE
 * --------------------------------------------------------------------
 * This is how we find out what controller needs to launch
 * based on the URL we got earilyer
 */
 
//Build the route
$route = new router($route);
//Check for false
if(list($load_class, $load_method) = $route->parse_routes()){
	//Check if we have a physical file for the route
	if(is_file(APPPATH .'/controllers/' . $load_class . EXT)){
		//We didn't get a number so lets check for a controller to load
		include_once(APPPATH .'/controllers/' . $load_class. EXT);
		//if just the controler is called run the index method
		//if(!isset($load_method) OR $load_method == ''){$load_method = 'index';}
		//check the method exists
		if(method_exists($load_class,$load_method)){
			//load the class
			$boot = new $load_class($route->url);
			//launch the method
			$boot->$load_method();
		}else{
			//if method doesnt exist, load 404 error
			boot_404();
		}
	}else{
		//if physical file dosent exist, load 404 error
		boot_404();
	}
	
}else{
	//if we cant find a route, load 404 error
	boot_404();
}



//  Call the 404 page, i put this in a funtion to keep things 
//  DRY and also becuase I was thinking of doing something I forgot now.
function boot_404(){	
	$boot = new BaseController;
	$boot->_VIEW_->load_view("404");
}


//If we have made it to this line something went wrong
//@TODO need to render some sort of code here letting the user know something went way wrong in a "nice" way

if(!class_exists('BaseController'))boot_404();

/* End of file core/launch.php */
/* Location: ./launch.php */