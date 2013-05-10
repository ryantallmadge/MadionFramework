<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class view{
	
	private $hold_html = '';         //Will hold the html code from the template
	private $hold_data = array();    //Will hold the data being passed to the template
	private $hold_session = array(); //Will hold the session data being passed to the template
	private $hold_globals = array(); //Will hold the global data being passed to the template
	private $hold_template = '';     //Will hold the template data
	public  $view_params = array();     //Will hold the template data
	
	
	function view(){
			$this->hold_globals['globals']['domain_name'] = $GLOBALS['DOMAIN_PARTS'];
			$this->hold_session['session'] = $_SESSION; 
	}
	
	//Get the view and data from the controller and put the together
	function display(){
		//assing the html to a hold so we can output it later
		$this->hold_html = $this->get_include_contents($this->hold_template);
		//Let us know if we failed to get anything from the template
		if($this->hold_html !== FALSE){		
			echo $this->hold_html;			
		}else{			
			return false;
		}
		
		//should never get here but doesnt hurt
		return false;
	}
	
	//method to display the template being called
	function load_view($template){$this->hold_template = APPPATH . '/views/' . $template . EXT;}
	
	//Set the values to be passes to the view
	function set($key, $value){
	
		$this->hold_data[$key] = $value;
		return true;
	
	}
	
	//Get the contents of the tempalate and combind them with the params 
	private function get_include_contents($filename) {
		if (is_file($filename)) {
			//Load the data set by controllers
			if(count($this->hold_data)    > 0) foreach ($this->hold_data as $sKey => $vValue)    {$$sKey = $vValue;$this->view_params[$sKey] = $vValue;} 
			//Load the session data, this is mostly for JSON and XML
			if(count($this->hold_session) > 0) foreach ($this->hold_session as $sKey => $vValue) {$$sKey = $vValue;$this->view_params[$sKey] = $vValue;}
			//Load the globals data
			if(count($this->hold_globals) > 0) foreach ($this->hold_globals as $sKey => $vValue) {$$sKey = $vValue;$this->view_params[$sKey] = $vValue;}
			//Load the views for the params
			$_VIEW_PARAMS_ = $this->view_params;
				//Start the object capture
				ob_start();
					//See what we are asking form and either transform or get from the views forlder
					if(router::$header_type_sent == 'AJAX' OR router::$header_override == 'AJAX'){
						include APPPATH . '/views/json' . EXT;
					}
					elseif(router::$header_override == 'XML'){
						include APPPATH . '/views/xml' . EXT; 
					}
					else{ 
						include $filename;
					}
					$contents = ob_get_contents();
				ob_end_clean();
			if(ENVIRONMENT == 'development'){echo "<!--";print_r($this->view_params);echo "-->";}
			return $contents;
		}
    	return false;
	}
	
	
	
}