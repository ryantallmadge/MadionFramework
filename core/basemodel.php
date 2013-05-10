<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * The base model will be where we start anything needed for all the models
 * We are loading:
 * Database, Log File
 * 
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * BaseModel Class
 */
 
 
 class BaseModel{
	public $_DB_;//Set the database container, this is what we use to talk to the database
	public $_LOGS_;//We will use this to write to the log files, except in production enviroment.

	//Starting the class lets call our manditory Classes
	function __construct(){
		/*
		 *---------------------------------------------------------------
		 * START LOGGING MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our log class so we can see what the code is doing
		 * Use: $this->_LOGS_->LogError('ERROR') --- HIGH ERROR
		 * Use: $this->_LOGS_->LogWarn('ERROR')  --- MEDIUM ERROR
		 * Use: $this->_LOGS_->LogInfo('ERROR')  --- LOW ERROR
		 */	
		 
		$this->_LOGS_ = $this->load_core('logs',BASEPATH.'/logs/log.txt');
		$this->_LOGS_->LogInfo("Script Started");
		
		/*
		 *---------------------------------------------------------------
		 * START DATABASE MANAGEMENT
		 *---------------------------------------------------------------
		 *
		 * Call our database class to start up database management
		 * this will let us handel all the database calles from the models
		 */
		 
		 $this->_DB_ = $this->load_core('db_class');
			if($this->_DB_->last_connect_error === false)//Check if we have a database connection
			{
				//If we have no connection, log the error and die
				$this->LogDatabaseError();
				die('We are currently offline');
			}

	}
	
	/*
	 *---------------------------------------------------------------
	 * LOAD OUR CONTROLLER HELPERS
	 *---------------------------------------------------------------
	 *
	 * These helpers will handel extra percedures needed by the models
	 */	
	 function load_helper($class_name) {
    	include_once (COREDIR . '/helpers/' . $class_name . EXT);
		return new $class_name();		
	 }
	 
	/*
	 *---------------------------------------------------------------
	 * LOAD OUR CORE CLASSES
	 *---------------------------------------------------------------
	 *
	 * These core classes needed by the models to run
	 */	
	 function load_core($class_name, $agrs_a = '', $agrs_b = '') {
		include_once (COREDIR . '/' . $class_name .EXT);
		return new $class_name($agrs_a,$agrs_b);
	 }
	
	/*
	 *---------------------------------------------------------------
	 * LOG DATABASE ERRORS
	 *---------------------------------------------------------------
	 *
	 * Write the database errors from the models.
	 * Use: $this->LogDatabaseError();
	 */	
	 function LogDatabaseError(){
			//Get the error and write it to the Log File
			$this->_LOGS_->LogError($this->_DB_->last_error);
			//Get the last query text and write it to the file
			$this->_LOGS_->LogError($this->_DB_->last_query);		
		//return true
		return true;
	 }
	
}