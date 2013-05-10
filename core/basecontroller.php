<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 *
 * The base controller will be where we start anything needed for all the controllers
 * We are loading:
 * Sessions, Request, Views, Email, Encryption, Geo Location, Log File
 *
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * BaseController Class
 */
  
 
 class BaseController{
	public $_SESSION_;//Hold the Session Class, used to override PHP session
	public $_REQUEST_;//Hold all of our POST vars, used to override PHP request
	public $_VIEW_;//Hold our view class, this is how we output information
	public $_EMAIL_;//How we communicate with the email class to send email from system
	public $_MCRYPT_;//Hold the class used to encrypt objects for storage
	public $_CRYPTKEY_;//Generates the sessions crypt key, this change on every load for security, never the same
	public $_GEOLOCATE_;//This is where we get our geo location information, only called on new session
	public $_LOGS_;//We will use this to write to the log files, except in production enviroment.

	/*
	 *---------------------------------------------------------------
	 * START THE BASE CONTROLER
	 *---------------------------------------------------------------
	 *
	 * This is where we will start everything needed to run the controllers
	 * Session, Logs, Views, Email, Encrypt, GeoLocate, Request, URL
	 */	

	function __construct(){
	
		/*
		 *---------------------------------------------------------------
		 * START SESSION MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our session class to start up sesssion management
		 * this will let us handel who people are and where they are coming from
		 */
				 
		$this->_SESSION_ = $this->load_core('session');
		
		/*
		 *---------------------------------------------------------------
		 * START REQUEST MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our request class to start up request management
		 * this is where we will get all of our _POST params from, 
		 * !!!!!DO NOT USE $_POST IN THE CODE!!!!
		 * we are only allowing _POST to happen, _GET query string will not be allowed
		 */
				 
		$this->_REQUEST_ = $this->load_core('request',POST_METHOD);
		
		/*
		 *---------------------------------------------------------------
		 * START VIEW MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our view class to start up GUI returned data
		 * this is where we will get all of params from the controllers and return them to the GUI
		 * this return does not need to be HTML is can be any type of data
		 */
				 
		$this->_VIEW_  = $this->load_core('view');
		
		/*
		 *---------------------------------------------------------------
		 * START EMAIL MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our email class so we can send out emails
		 */
			 
		$this->_EMAIL_ = $this->load_core('email');
		
		/*
		 *---------------------------------------------------------------
		 * START ENCRYPTION MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our encrypt class so we can encrypt data objects
		 */		 
		 
		$this->_MCRYPT_   = $this->load_third_party('cryptastic');
		$this->_CRYPTKEY_ = $this->_MCRYPT_->pbkdf2(rand(100,10000), rand(100,10000), 1000, 32);
		
		/*
		 *---------------------------------------------------------------
		 * START GEOLOCATION MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our geo location class so we can see where the user is coming from
		 */	
		 
		$this->_GEOLOCATE_ = $this->load_third_party('geoplugin');
		if(!isset($_SESSION['GEOLOCATE']))
			$this->_GEOLOCATE_->locate();
			
		
		/*
		 *---------------------------------------------------------------
		 * START LOGGING MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our log class so we can see what the code is doing
		 */	
		$this->_LOGS_ = $this->load_core('logs',BASEPATH.'/logs/log.txt');
		$this->_LOGS_->LogInfo("Script Started");		/*
		 
		 *---------------------------------------------------------------
		 * START URL MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our url class so we can help the controller go places
		 */	
		$this->_URL_ = $this->load_helper('url');
		
		/*
		 *---------------------------------------------------------------
		 * CHECK IF WE NEED TO BE LOGGED IN FOR WHERE WE ARE GOING
		 		if(router::$userlogin AND !isset($_SESSION['member_loged_in'])){$this->_URL_->redirect('/login');exit;}
				if(router::$companylogin AND !isset($_SESSION['company_loged_in'])){$this->_URL_->redirect('/companylogin');exit;}
				if(router::$push  AND  isset($_SESSION['member_loged_in'])){$this->_URL_->redirect('/account');exit;}
		 *---------------------------------------------------------------
		 *
		 */	


	}//End the construct of the base controller
	
	
	/*
	 *---------------------------------------------------------------
	 * LOAD OUR CONTROLLER HELPERS
	 *---------------------------------------------------------------
	 *
	 * These helpers will handel extra percedures needed by the controllers
	 */	
	 function load_helper($class_name) {
    	include_once (COREDIR . '/helpers/' . $class_name . EXT);
		return new $class_name();		
	 }
	
	/*
	 *---------------------------------------------------------------
	 * LOAD OUR DATABSE MODELS
	 *---------------------------------------------------------------
	 *
	 * These helpers will handel extra percedures needed by the controllers
	 */	
	 function load_model($class_name) {
    	include_once (APPPATH . '/models/' . $class_name . EXT);
		return new $class_name();
	 }
	
	/*
	 *---------------------------------------------------------------
	 * LOAD OUR CORE CLASSES
	 *---------------------------------------------------------------
	 *
	 * These core classes needed by the controllers to run
	 */	
	 function load_core($class_name, $agrs_a = '', $agrs_b = '') {
		include_once (COREDIR . '/' . $class_name .EXT);
		return new $class_name($agrs_a,$agrs_b);
	 }
	 
	 /*
	 *---------------------------------------------------------------
	 * LOAD OUR THIRD PARTY CLASSES
	 *---------------------------------------------------------------
	 *
	 * These thrid party classes needed by the controllers to run
	 */	
	 function load_third_party($class_name, $agrs_a = '', $agrs_b = '') {
		include_once (COREDIR . '/thirdparty/' . $class_name .EXT);
		return new $class_name($agrs_a,$agrs_b);
	 }
	 
	/*
	 *---------------------------------------------------------------
	 * DESTRUCT THE BASE CONTROLLER
	 *---------------------------------------------------------------
	 *
	 * When everything is said and done, lets output what we got
	 * Show the view
	 * Log the script has completed
	 */	
	 function __destruct(){
		//Display the view
		$this->_VIEW_->display();
		//Log the script has ended
		$this->_LOGS_->LogInfo("Script Ended");
		//No return fool this is destruct!!!!
	 }

}