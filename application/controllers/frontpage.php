<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');//Dont run this without everything else


/**
 *
 * @package		Madi-On
 * @author		Ryan Tallmadge
 * @since		Version 1.0
 *
 * @subpackage	 Application
 * @category	 Controller
 * @description  Frontpage controller. This will be the default controller for users to the frontpage
 * @var uri public | Will hold the URI params from the launcher
 * @var user_model public | Will hold the value of the user_model class
 */
 
 
	
class frontpage extends BaseController{
	/***Public vars****/
	public $uri;//Hold the params passed from the URL
	
	/***Model vars****/
	public $user_model;//Will hold the company_model database information
	
	//Lets construct the class by starting the parent class
	//We also set the URI for the controller, this is no needed unless we need a param from the URI
	function frontpage($load){
	/*PARENT*/
		parent::__construct();//Start parent class, needed to make things work
	/*SET PARAMS*/
		$this->uri           = $load;//Set the URI params
	/*LOAD MODELS*/
		//Example: $this->user_model = $this->load_model('user_model');//Load company model
	/*LOAD HELPERS*/
		//Example: $this->upload     = $this->load_helper('uploads');
		
        return true;
	}
    
    //index controler
    function index(){
        //Load the view
        $this->_VIEW_->load_view('frontpage');
        //return the controler
        return true;
    }
    


	
}

/* End of file frontpage.php */
/* Location: application/controllers/frontpage.php */